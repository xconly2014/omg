<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use think\Hook;

/**
 * 签到
 * @package app\api\service\wx
 */
class Attend extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64'
    ];

    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        $param = [
            'user'=> $user
        ];
        Hook::exec('app\\api\\behavior\\events\\Attend','run',$param);
        return [];
    }
}