<?php


namespace app\common\redis;

use app\lib\redis\RedisModel;

class AuthCode extends RedisModel
{
    protected $options = [
        'string' => [
            'emailAuthCode' => [
                'key_prefix' => 'EmailAuthCode',
                'expire' => 900,
            ],
            'telAuthCode' => [
                'key_prefix' => 'TelAuthCode',
                'expire' => 900,
            ],
        ],
    ];


    /**
     * @param $key
     * @return $this
     */
    public function init($key)
    {
        $this->setId( $key );
        return $this;
    }

    // ----------------------------------------------------------------------------- tel code
    public function getTelCode() {
        $string = $this->string('TelAuthCode', $this->getId());
        return $string->get();
    }

    public function ttlTelCode() {
        $string = $this->string('TelAuthCode', $this->getId());
        return $string->ttl();
    }

    public function setTelCode($code) {
        $string = $this->string('TelAuthCode', $this->getId());
        return $string->set($code);
    }

    public function delTelCode() {
        $string = $this->string('TelAuthCode', $this->getId());
        return $string->del();
    }

    // ----------------------------------------------------------------------------- email code
    public function getEmailCode() {
        $string = $this->string('EmailAuthCode', $this->getId());
        return $string->get();
    }

    public function ttlEmailCode() {
        $string = $this->string('EmailAuthCode', $this->getId());
        return $string->ttl();
    }

    public function setEmailCode($code) {
        $string = $this->string('EmailAuthCode', $this->getId());
        return $string->set($code);
    }

    public function delEmailCode() {
        $string = $this->string('EmailAuthCode', $this->getId());
        return $string->del();
    }
}