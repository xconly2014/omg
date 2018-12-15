<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use think\Config;

class UserTel extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
        'tel_prefix'  => 'require|max:10',
        'tel_number'  => 'require|number|max:32',
        'auth'      => 'number'
    ];

    public function api() {
        $this->chkTelAuth();
        $user = $this->findAndUpdateUser();
        $res = $this->sendUser($user);
        $data = $res->data;
        return [
            'uid' => $data->uid,
            'point' => $data->point,
            'openid' => $data->openid
        ];
    }

    protected function sendUser($user) {
        $config = Config::get('api.wx');
        try {
            $res = S('ABEVipApi')->send('tp/wxLogin', [
                'appID' => $config['AppID'],
                'openid' => $user->openid,
                'tel_prefix' => $user->tel_prefix,
                'tel_number' => $user->tel_number
            ]);
            $this->callEventBindPhone($user);
        } catch (\Exception $e) {
            if ($e->getCode() == 13002) {
                $this->callEventBindPhone($user);
            }
            throw $e;
        }
        return $res;
    }

    /**
     * 用户实体
     * @return mixed
     */
    protected function findAndUpdateUser() {
        $user = S('user')->getUser($this->param('openid'));
        $user->tel_prefix = $this->param('tel_prefix', 86);
        $user->tel_number = $this->param('tel_number');
        $user->save();
        return $user;
    }

    /**
     * 校验验证码
     * @throws \app\common\exception\CodeException
     */
    protected function chkTelAuth() {
        if ($this->param('auth')) {
            S('AuthCode')->verifyByTelNumber(
                $tel_prefix  = $this->param('tel_prefix'),
                $tel_account = $this->param('tel_account'),
                $this->param('auth')
            );
        }
    }

    /**
     * 触发事件 绑定手机号
     * @param $user
     */
    protected function callEventBindPhone($user) {
        if (!$user->is_tel_bind) {
            $_param = [
                'user' => $user,
            ];
            \think\Hook::exec('app\\api\\behavior\\events\\BindTel','run',$_param);
        }
    }
}