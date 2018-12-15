<?php
namespace app\api\service;

use app\common\exception\Factory;
use think\Config;


class ApiAuth
{
    /**
     * 生成签名
     * @param $secret_key
     * @param $content
     * @return string
     */
    public function sign($secret_key, $content) {
        return md5(md5($content) . $secret_key);
    }

    public function generateSign($content) {
        $secret_key = $this->getSecretKey();
        return $this->sign($secret_key, $content);
    }

    /**
     * 验证签名
     * @param $content
     * @param $sign
     * @return bool
     */
    public function verifySign($content, $sign) {
        $secret_key = $this->getSecretKey();
        if(!$secret_key) return false;
        $refer = $this->sign($secret_key, $content);
        return ($refer === $sign);
    }

    public function getSecretKey() {
        // $version = \think\Request::instance()->param('version');
        $secret = Config::get('api.api_secret');
        if(!$secret) {
            throw Factory::resourceNotFound('api SecretKey');
        }
        return $secret;
    }
}