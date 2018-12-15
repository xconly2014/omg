<?php


namespace app\api\service\wx;


use app\api\service\AbstractApiHandler;
use app\lib\Log;
use think\Config;
use think\Exception;

class OnLogin extends AbstractApiHandler
{
    public $rule = [
        'code'  =>  'require|alphaDash|min:16',
        'rouser' => 'max:64'
    ];

    public function api() {
        $param = S('wxApi')->jscode2session($this->param('code'));
        $is_newbie = false;
        $user = S('user')->getUserFromWxSessionData($param, $is_newbie);
        if (!$user) {
            Log::error('param: ' . json_encode($param, JSON_UNESCAPED_UNICODE));
            throw new Exception('登录失败');
        }
        if ($is_newbie) {
            $this->sendUserToABE($user);
        }
        $this->beRoused($user);
        $user->last_login_time = time();
        $user->save();
        $this->callUnionidEvent($user);

        $data = S('user')->simpleInfo($user);
        $data['is_newbie'] = $is_newbie;
        return $data;
    }

    protected function sendUserToABE($user) {
        $config = Config::get('api.wx');

        $data['appID'] = $config['AppID'];
        $data['openid'] = $user->openid;
        if ($user->unionid) {
            $data['unionID'] = $user->unionid;
        }
        S('ABEVipApi')->send('tp/wxLogin', $data);
    }

    /**
     * 被唤醒
     * @param \app\common\model\User $sleeper 沉睡者，被唤醒的人
     */
    protected function beRoused($sleeper) {
        if ($this->param('rouser') && $this->param('rouser') !== $sleeper->openid) {
            $rouser = M('user')->findByOpenid($this->param('rouser'));
            if ($rouser) {
                $_param = [
                    'user' => $rouser,
                    'target' => $sleeper
                ];
                \think\Hook::exec('app\\api\\behavior\\events\\Rouse','run',$_param);
            }
        }
    }

    /**
     * 触发关注公众号事件
     * @param $user
     */
    protected function callUnionidEvent($user) {
        if ($user->unionid && !$user->is_follow_our) {
            $_param = [
                'user' => $user,
            ];
            \think\Hook::exec('app\\api\\behavior\\events\\FollowOur','run',$_param);
        }
    }
}