<?php

namespace SMPP3\Schema;
/**
 * SMPP TLV_MESSAGE_STATE 值枚举
 * Tag: 0x0427
 */
class SmppMessageState {
    const SCHEDULED     = 0x01;          // 已调度 (消息将在未来时间发送)
    const ENROUTE       = 0x02;          // 发送中 (消息已转发到下一个SMSC)
    const DELIVERED     = 0x03;          // 已送达 (消息已成功送达目的地)
    const EXPIRED       = 0x04;          // 已过期 (消息在有效期内未送达)
    const DELETED       = 0x05;          // 已删除 (消息已被SMSC删除)
    const UNDELIVERABLE = 0x06;          // 无法送达 (消息无法送达)
    const ACCEPTED      = 0x07;          // 已接受 (消息已被SMSC接受但未确认送达)
    const UNKNOWN       = 0x08;          // 未知状态 (SMSC未知状态)
    const REJECTED      = 0x09;          // 已拒绝 (消息被拒绝)
    
    const NAME_SCHEDULED     = "SCHEDULED";
    const NAME_ENROUTE       = "ENROUTE";
    const NAME_DELIVERED     = "DELIVERED";
    const NAME_EXPIRED       = "EXPIRED";
    const NAME_DELETED       = "DELETED";
    const NAME_UNDELIVERABLE = "UNDELIVERABLE";
    const NAME_ACCEPTED      = "ACCEPTED";
    const NAME_UNKNOWN       = "UNKNOWN";
    const NAME_REJECTED      = "REJECTED";
    
    /**
     * 获取所有dlv消息tlv.message_state状态的关联数组
     */
    public static function getAllMessageStates(): array {
        return [
            self::SCHEDULED     => self::NAME_SCHEDULED,
            self::ENROUTE       => self::NAME_ENROUTE,
            self::DELIVERED     => self::NAME_DELIVERED,
            self::EXPIRED       => self::NAME_EXPIRED,
            self::DELETED       => self::NAME_DELETED,
            self::UNDELIVERABLE => self::NAME_UNDELIVERABLE,
            self::ACCEPTED      => self::NAME_ACCEPTED,
            self::UNKNOWN       => self::NAME_UNKNOWN,
            self::REJECTED      => self::NAME_REJECTED
        ];
    }
    
    /**
     * 获取dlv中的short_message.stat取值列表
     * @return array{string:string}
     */
    public static function getAllShortStates(): array {
        return [
            "DELIVRD" => self::NAME_DELIVERED,
            "EXPIRED" => self::NAME_EXPIRED,
            "DELETED" => self::NAME_DELETED,
            "UNDELIV" => self::NAME_UNDELIVERABLE,
            "ACCEPTD" => self::NAME_ACCEPTED,
            "UNKNOWN" => self::NAME_UNKNOWN,
            "REJECTD" => self::NAME_REJECTED
        ];
    }
    
    /**
     * 获取dlv中的tlv.message_state状态描述
     */
    public static function getMessageStateDescription(int $state): string {
        $descriptions = [
            self::SCHEDULED     => '已调度 (消息将在未来时间发送)',
            self::ENROUTE       => '发送中 (消息已转发到下一个SMSC)',
            self::DELIVERED     => '已送达 (消息已成功送达目的地)',
            self::EXPIRED       => '已过期 (消息在有效期内未送达)',
            self::DELETED       => '已删除 (消息已被SMSC删除)',
            self::UNDELIVERABLE => '无法送达 (消息无法送达)',
            self::ACCEPTED      => '已接受 (消息已被SMSC接受但未确认送达)',
            self::UNKNOWN       => '未知状态 (SMSC未知状态)',
            self::REJECTED      => '已拒绝 (消息被拒绝)'
        ];
        return $descriptions[$state] ?? '未知状态值';
    }
    
    /**
     * 获取dlv中的tlv.stort_message.stat状态描述
     *
     * @param string $state
     *
     * @return string
     */
    public static function getShortStateDescription(string $state): string {
        $descriptions = [
            "DELIVRD" => '已送达 (消息已成功送达目的地)已调度 (消息将在未来时间发送)',
            "EXPIRED" => '已过期 (消息在有效期内未送达)发送中 (消息已转发到下一个SMSC)',
            "DELETED" => '已删除 (消息已被SMSC删除)',
            "UNDELIV" => '无法送达 (消息无法送达)',
            "ACCEPTD" => '已接受 (消息已被SMSC接受但未确认送达)',
            "UNKNOWN" => '未知状态 (SMSC未知状态)',
            "REJECTD" => '已拒绝 (消息被拒绝)',
        ];
        return $descriptions[$state] ?? '未知状态值';
    }
    
    /**
     * 获取tlv.message_state状态名称
     */
    public static function getMessageStateName(int $state): string {
        $states = self::getAllMessageStates();
        return $states[$state] ?? 'UNKNOWN';
    }
    
    /**
     * 获取short_message.state的状态名
     *
     * @param string $state
     *
     * @return string
     */
    public static function getShortStateName(string $state): string {
        $states = self::getAllShortStates();
        return $states[$state] ?? 'UNKNOWN';
    }
}

