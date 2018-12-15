<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use think\Config;

class UserInfo extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
        'unionID'    => 'max:64',
        'referrer'  => 'max:64',
        'nickname'  => 'max:32',
        'avatar'  => 'url',
        'gender'  => 'in:0,1,2',
        'province'  => 'max:32',
        'city'  => 'max:32',
        'country'  => 'max:32',
        'tel_prefix'  => 'max:10',
        'tel_number'  => 'number|max:32',
        'auth'      => 'number'
    ];

    public function api() {
        $user = $this->findAndUpdateUser();
        $data = $this->param();
        $config = Config::get('api.wx');

        $data['appID'] = $config['AppID'];
        $res = S('ABEVipApi')->send('tp/wxLogin', $data);
        $data = $res->data;

        $this->callReferralEvent($user);
        return [
            'uid' => $data->uid,
            'point' => $data->point,
            'openid' => $data->openid
        ];
    }

    /**
     * @return mixed
     */
    protected function findAndUpdateUser() {
        $user = S('user')->getUserOrInstanceByOpenId($this->param('openid'));
        $this->param('unionID') && $user->unionid = $this->param('unionID');
        $this->param('nickname') && $user->nickname = $this->param('nickname');
        $this->param('avatar') && $user->avatar = $this->param('avatar');
        $this->param('gender') && $user->gender = $this->param('gender');
        $this->param('province') && $user->province = $this->param('province');
        $this->param('city') && $user->city = $this->param('city');
        $this->param('country') && $user->country = $this->param('country');

        if ($this->param('referrer')
            && $user->openid != $this->param('referrer')
            && !$user->referrer) {
            $user->referrer = $this->param('referrer');
        }
        $user->save();
        return $user;
    }

    /**
     * 触发邀请事件
     * @param $user
     */
    protected function callReferralEvent($user) {
        if ($user->referrer) {
            $_param = [
                'user' => $user,
            ];
            \think\Hook::exec('app\\api\\behavior\\events\\SetReferral','run',$_param);
        }
    }
}