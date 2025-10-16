<?php

namespace SMPP3\Schema;

class NumberAddress {
    /**
     * 号码地址
     * @var string
     */
    protected string $number;
    /**
     * type of number
     * @example 0    TON_UNKNOWN    未知号码类型（默认）
     * 1    TON_INTERNATIONAL    国际号码（如 +8613812345678）
     * 2    TON_NATIONAL    国内号码（如 13812345678）
     * 3    TON_NETWORK    网络专用号码（如运营商短号）
     * 4    TON_SUBSCRIBER    用户短号（子地址）
     * 5    TON_ALPHANUMERIC    字母数字（如 CompanyName）
     * 6    TON_ABBREVIATED    缩写号码（较少使用）
     * 7-15    （保留）    保留值，未来可能扩展
     * @var int
     */
    protected int $ton;
    /**
     * Numbering Plan
     * @var int
     * @example 0    NPI_UNKNOWN    未知编号计划（默认）
     * 1    NPI_ISDN (E.164)    标准电话号码（E.164 格式，如 +8613812345678）
     * 2    NPI_DATA (X.121)    数据编号（X.121，如 IP 地址）
     * 3    NPI_TELEX    电传号码（较少使用）
     * 4    NPI_LAND_MOBILE    陆地移动号码（如 GSM 号码）
     * 5    NPI_NATIONAL    国家特定编号计划
     * 6    NPI_PRIVATE    私有编号计划（企业内部）
     * 7    NPI_ERMES    ERMES 寻呼系统
     * 8    NPI_INTERNET (IP)    Internet 编号（如 IP 地址）
     * 9    NPI_WAP    WAP 客户端编号
     * 10-15    （保留）    保留值
     */
    protected int $npi;
    
    public function __construct(string $number, int $ton = 0, int $npi = 0) {
        $this->number = $number;
        $this->ton    = $ton;
        $this->npi    = $npi;
    }
    
    public function getNumber(): string {
        return $this->number;
    }
    
    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }
    
    public function getTon(): int {
        return $this->ton;
    }
    
    public function setTon($ton) {
        $this->ton = $ton;
        return $this;
    }
    
    public function getNpi(): int {
        return $this->npi;
    }
    
    public function setNpi($npi) {
        $this->npi = $npi;
        return $this;
    }
}