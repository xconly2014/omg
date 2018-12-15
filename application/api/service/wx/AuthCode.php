<?php


namespace app\api\service\wx;


use app\api\service\AbstractApiHandler;
use app\common\exception\Factory;
use think\Cache;

class AuthCode extends AbstractApiHandler
{
    public $rule = [
        'openid' => 'require|max:64',
        'tel_prefix'  =>  'number|max:8',
        'tel_number'  =>  'require|number|max:32'
    ];

    public function api() {
        $key = 'AuthCodeLock:' . $this->param('openid');
        $this->chkIsLock($key);
        /*$code = */S('authCode')->sendToTelNumber($this->param('tel_prefix', '86'), $this->param('tel_number'));

        Cache::set($key, '1', 60);
        return [];
    }

    protected function chkIsLock($key) {
        $is_lock = Cache::get($key);
        if ($is_lock) {
            throw Factory::invalidToken('Please operate later.');
        }
    }
}