<?php

namespace SMPP3;

use Closure;
use SMPP3\Schema\NumberAddress;
use Swoole\Coroutine;
use SMPP3\Util\Receiver;
use SMPP3\Util\Transceiver;
use SMPP3\Util\Transmitter;

class SMPP3Client {
    /** @var Transceiver */
    protected $transceiver = null;
    /** @var Receiver */
    protected $receiver = null;
    /** @var Transmitter */
    protected $transmitter = null;
    
    protected $config;
    
    protected $sequenceNum;
    protected $startBindTime;
    
    public int    $errCode = 0;
    public string $errMsg  = '';
    
    public function __construct($config) {
        $this->checkConfig($config);
    }
    
    /**
     * getTransceiver
     * @return \SMPP3\Util\Transceiver
     */
    public function getTransceiver() {
        return $this->transceiver;
    }
    
    /**
     * getTransmitter
     * @return Transmitter
     */
    public function getTransmitter() {
        return $this->transmitter;
    }
    
    /**
     * getReceiver
     * @return Receiver
     */
    public function getReceiver() {
        return $this->receiver;
    }
    
    /**
     * getConfig
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getConfig($key, $default = null) {
        return $this->config[$key] ?? $default;
    }
    
    /**
     * getStartBindTime
     * @return mixed
     */
    public function getStartBindTime() {
        return $this->startBindTime;
    }
    
    
    /**
     * checkConfig
     *
     * @param $config
     */
    protected function checkConfig($config) {
        $config['interface_version'] = empty($config['interface_version']) ? 52 : (int)$config['interface_version'];
        $config['addr_ton']          = empty($config['addr_ton']) ? 1 : (int)$config['addr_ton'];
        $config['addr_npi']          = empty($config['addr_npi']) ? 1 : (int)$config['addr_npi'];
        $config['address_range']     = empty($config['address_range']) ? '' : $config['address_range'];
        //系统
        $config['system_type'] = empty($config['system_type']) ? 'WWW' : $config['system_type'];
        //模式
        $config['mode'] = empty($config['mode']) ? 'transceiver' : $config['mode'];
        
        $this->sequenceNum = $config['sequence_start'];
        $this->config      = $config;
    }
    
    
    /**
     * login
     *
     * @param $ip
     * @param $port
     * @param $account
     * @param $pwd
     * @param $timeout
     *
     * @return array|bool
     * @throws \Exception
     */
    public function login($ip, $port, $account, $pwd, $timeout) {
        $this->startBindTime = time();
        $mode = $this->getConfig("mode", "transceiver");
        switch ($mode) {
            case "transceiver":
                //接收一体模式
                $this->transceiver = new Transceiver($this);
                return $this->transceiver->bind(
                    $ip, $port, $account, $pwd, $timeout,
                    function () {
                        //如果链接成功则创建探活
                        Coroutine::create([$this->transceiver, 'doEnquireLink']);
                    }
                );
            case "transmitter:receiver":
                //分离模式
                $this->transmitter = new Transmitter($this);
                return $this->transmitter->bind($ip, $port, $account, $pwd, $timeout, $this->createReceiver(...func_get_args()));
            case "transmitter":
                $this->transmitter = new Transmitter($this);
                return $this->transmitter->bind($ip, $port, $account, $pwd, $timeout, function () {
                    Coroutine::create([$this->transmitter, 'doEnquireLink']);
                });
            case "receiver":
                $this->receiver = new Receiver($this);
                return $this->receiver->bind($ip, $port, $account, $pwd, $timeout, function () {
                    Coroutine::create([$this->receiver, 'doEnquireLink']);
                });
            default:
                return false;
        }
    }
    
    /**
     * logout
     */
    public function logout() {
        $this->transceiver?->unbind();
        $this->transmitter?->unbind();
        $this->receiver?->unbind();
    }
    
    /**
     * createReceiver
     * 分离模式下额外创建一个收信连接
     *
     * @param $ip
     * @param $port
     * @param $account
     * @param $pwd
     * @param $timeout
     *
     * @return Closure
     */
    protected function createReceiver($ip, $port, $account, $pwd, $timeout) {
        return function () use ($ip, $port, $account, $pwd, $timeout) {
            //如果成功则需要进行receiver的bind
            $this->receiver = new Receiver($this);
            
            return $this->receiver->bind($ip, $port, $account, $pwd, $timeout, function () {
                //如果成功则创建两个客户端的探活
                Coroutine::create([$this->transmitter, 'doEnquireLink']);
                
                Coroutine::create([$this->receiver, 'doEnquireLink']);
            }, function () {
                //如果失败，则需要断开transmitter
                $this->transmitter->unbind();
            });
        };
    }
    
    /**
     * 读取一条消息
     * clientTransmitterListen
     *
     * @param int $timeout
     *
     * @return bool|array
     */
    public function clientTransmitterListen(int $timeout): bool|array {
        if (!$this->transmitter->getIsListening()) {
            Coroutine::create([$this->transmitter, 'listen']);
        }
        $msg = $this->transmitter->getPduChannel()->pop($timeout);
        // if ($msg === false) {
        //     $this->transmitter->customError($this->transmitter->getPduChannel()->errCode, "读取通道失败");
        // }
        return $msg;
    }
    
    /**
     * 读取一条消息
     *
     * @param int $timeout
     *
     * @return bool|array
     */
    public function clientReceiverListen(int $timeout): bool|array {
        if (!$this->receiver->getIsListening()) {
            Coroutine::create([$this->receiver, 'listen']);
        }
        $msg = $this->receiver->getPduChannel()->pop($timeout);
        // if ($msg === false) {
        //     $this->receiver->customError($this->receiver->getPduChannel()->errCode, "读取通道失败");
        // }
        return $msg;
    }
    
    /**
     * 读取一条消息
     *
     * @param int $timeout
     *
     * @return bool|array
     */
    public function clientTransceiverListen(int $timeout): bool|array {
        if (!$this->transceiver->getIsListening()) {
            Coroutine::create([$this->transceiver, 'listen']);
        }
        $msg = $this->transceiver->getPduChannel()->pop($timeout);
        // if ($msg === false) {
        //     $this->transceiver->customError($this->transceiver->getPduChannel()->errCode, "读取通道失败");
        // }
        return $msg;
    }
    
    /**
     * submit
     *
     * @param string $mobile 收件人
     * @param string $text 内容
     * @param string $src 发件人
     *
     * @return array
     */
    public function submit($mobile, $text, $src) {
        $client = $this->transceiver;
//            $client = $this->transmitter;
        
        return $client->submitSm(new NumberAddress($src, 5, 0), new NumberAddress($mobile, 1, 1), $text);
    }
}