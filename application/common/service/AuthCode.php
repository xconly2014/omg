<?php


namespace app\common\service;


use app\common\exception\Factory;
use app\lib\sms\Aliyun;

class AuthCode
{
    /**
     * Check verification code
     * @param $tel_prefix
     * @param $tel_number
     * @param $auth_code
     * @throws \app\common\exception\LangException
     */
    public function verifyByTelNumber($tel_prefix, $tel_number, $auth_code) {
        $full_tel = '+'.$tel_prefix.$tel_number;
        $auth = $tel_number ? R('AuthCode')->init($full_tel)->getTelCode() : null;
        if(!$auth || $auth_code !== $auth) {
            throw Factory::error('Wrong auth code', 12002);
        }
    }

    /**
     * @param $tel_prefix
     * @param $tel_number
     * @return integer
     */
    public function clearByTelNumber($tel_prefix, $tel_number) {
        $full_tel = '+'.$tel_prefix.$tel_number;
        return R('AuthCode')->init($full_tel)->delTelCode();
    }

    /**
     * Send the auth code to the user by phone number
     * @param $tel_prefix
     * @param $tel_number
     * @throws \app\common\exception\LangException
     * @return int
     */
    public function sendToTelNumber($tel_prefix, $tel_number) {
        $full_tel = '+'.$tel_prefix.$tel_number;
        R('AuthCode')->init($full_tel);
        $ttl = R('AuthCode')->ttlTelCode();
        if($ttl >= 900 - 60) {
            return R('AuthCode')->getTelCode();
        }

        $code = mt_rand(1000, 9999);
        $this->sendSmsByAliyun($tel_number, $code);
        R('AuthCode')->setTelCode($code);

        return $code;
    }

    protected function sendSMSByAliyun($tel_number, $code) {
        $sms = new Aliyun();
        $resp = $sms->template('user_auth')
            ->to($tel_number)
            ->templateParam('code', $code)
            ->send();
        if($resp->Code !== 'OK') {
            $msg = 'errorCode:' . $resp->Code;
            throw Factory::error($msg);
        }
    }
}