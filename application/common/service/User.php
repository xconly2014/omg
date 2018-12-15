<?php
namespace app\common\service;

use app\common\exception\Factory;
use think\Cache;
use think\Config;

class User
{
    public function getUser($openid) {
        $user = M('user')->findByOpenid($openid);
        if (!$user) {
            throw Factory::error('Please log in first', 13001);
        }
        return $user;
    }

    /**
     * 从sessionData获取用户实体
     * 注意，该方法不保存用户
     * @param $data
     * @param $is_newbie
     * @return null
     */
    public function getUserFromWxSessionData($data, &$is_newbie) {
        $is_newbie = false;
        if (isset($data['openid']) && isset($data['session_key'])) {
            $openid = $data['openid'];
            $session_key = $data['session_key'];
            $unionid = isset($data['unionid']) ? $data['unionid'] : null;
            $user = null;
            if ($unionid) {
                $user = M('user')->where('unionid', $unionid)->find();
            }
            if (!$user){
                $user = M('user')->where('openid', $openid)->find();
            }
            if (!$user) {
                $user = M('user')->getInstance();
                $is_newbie = true;
            }
            $user->openid = $openid;
            $user->session_key = $session_key;
            $user->unionid = $unionid;
            return $user;
        }
        return null;
    }

    /**
     * 以openid获得用户实体
     * @param $openid
     * @return mixed
     */
    public function getUserOrInstanceByOpenId($openid) {
        $user = M('user')->where('openid', $openid)->find();
        if (!$user) {
            $user = M('user')->getInstance();
            $user->openid = $openid;
            $user->is_create = 1;
        }
        return $user;
    }

    /**
     * @param \app\common\model\User $user
     * @return array
     */
    public function simpleInfo(\app\common\model\User $user) {
        return array(
            'openid' => $user->openid,
            'unionid' => $user->unionid,
            'tel_prefix' => $user->tel_prefix,
            'tel_number' => $user->tel_number,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'gender' => $user->gender,
            'province' => $user->province,
            'city' => $user->city,
            'country' => $user->country,
        );
    }

    /**
     * 我的积分
     * @param \app\common\model\User $user
     * @return mixed|string
     */
    public function getUserPoint(\app\common\model\User $user) {
        $cacheKey = 'user:point:' . $user->openid;
        $point = Cache::get($cacheKey);
        if(!$point) {
            $point = $this->refreshUserPoint($user);
        }
        return $point;
    }

    /**
     * 刷新获取我的积分
     * @param $user
     * @return mixed|string
     */
    public function refreshUserPoint($user) {
        $cacheKey = 'user:point:' . $user->openid;
        $config = Config::get('api.wx');
        $point = '0';

        $res = S('ABEVipApi')->send('tp/wxPoint', [
            'appID' => $config['AppID'],
            'openid' => $user->openid
        ]);
        if ($res->code == 1) {
            $point = $res->data->point;
            $pending_point = M('withdraw')
                ->where('openid', $user->openid)
                ->where('trade_type', 'POINT')
                ->where('transfer_status', '<=', 1)
                ->where('is_point_change', 0)
                ->sum('point');
            if ($pending_point) {
                $point -= $pending_point;
            }
            $point = sprintf('%.4f', floor($point * 10000) / 10000);
            Cache::set($cacheKey, $point, 300);
        }
        return $point;
    }

    /**
     * 用户已收益的积分
     * @param $openid
     * @return string
     */
    public function getUserIncomePoint($openid) {
        $cacheKey = 'User:incomePoint:' . $openid;
        $config = Config::get('api.wx');

        $result = Cache::get($cacheKey);
        if (!$result) {
            $res = S('ABEVipApi')->send('tp/wxIncomePoint', [
                'appID' => $config['AppID'],
                'openid' => $openid
            ]);
            if ($res->code == 1) {
                $result = $res->data->point;
                Cache::set($cacheKey, $result, 300);
            }
        }
        return $result ? $result : '0';
    }


    /**
     * 保存 formId, 以供发送模板消息
     * https://developers.weixin.qq.com/miniprogram/dev/component/form.html
     * @param $openid
     * @param $formId
     * @return mixed
     */
    public function saveFormID($openid, $formId) {
        if ($formId) {
            $entity = M('formId')->getInstance();
            $entity->openid = $openid;
            $entity->formId = $formId;
            return $entity->save();
        }
        return 0;
    }

    public function delFormID($openid, $formId) {
        M('formId')->where('openid', $openid)->where('formId', $formId)->delete();
    }

    /**
     * 被邀请人信息
     * @param $user
     * @param $row
     * @return array
     */
    public function simpleReferrerRow($user, $row) {
        // 不活跃用户
        return [
            'openid' => $user->openid,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'is_valid' => $row->is_share_group_success ? 1 : 0, // 是否为有效邀请
            'is_sleepy' => $this->isSleepy($user) ? 1 : 0,
            'create_time' => $row->create_time,
            'event_point' => $row->event_point
        ];
    }

    /**
     * 有效邀请人数
     * @param $user
     * @return mixed
     */
    public function realReferrerNum($user) {
        return M('userReferrer')
            ->where('user_id', $user->id)
            ->where('level', 1)
            ->where('is_share_group_success', 1)
            ->cache()
            ->count();
    }

    /**
     * x天未登录，不活跃用户
     * @param $user
     * @return bool
     */
    public function isSleepy($user) {
        return ( time() - $user->last_login_time ) / 86400 >= 5;
    }
}