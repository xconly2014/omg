<?php


namespace app\api\behavior\events;

use think\Cache;

class SetReferral
{
    /**
     * 引荐人
     * @param $param
     */
    public function run(&$param) {
        $target   = $param['user']; // 被引荐人
        if ($target && $target->referrer && $target->referrer != $target->openid) {
            $referrer = M('user')->findByOpenid($target->referrer); // 引荐人
            if ($referrer) {
                $this->onReferral($referrer, $target, 1); // 一级邀请
                if ($referrer->referrer && $referrer->referrer != $target->openid) {
                    $referrer =M('user')->findByOpenid($referrer->referrer); // 引荐人的引荐人
                    if ($referrer) {
                        $this->onReferral($referrer, $target, 2); // 二级邀请
                    }
                }
            }
        }
    }

    protected function onReferral($user, $target, $level = 1) {
        $lockKey = 'onReferral:' . $level . ':'  . $target->id;
        if (!Cache::has($lockKey)) {
            Cache::set($lockKey, 1, 3);
            $exists = M('UserReferrer')
                ->where('level', $level)
                ->where('target_id', $target->id)
                ->count();
            if (!$exists) {
                $event_hash = $level === 1 ? 'omg_referrer_v1' : 'omg_referrer_v2';
                $event = M('activityEvent')->where('hash', $event_hash)->find();
                $event_point = $event && $event->point ? $event->point : 0;

                if ($event_hash && $level !== 1) { // level = 1 改为需进一步完成分享到群任务才计算奖励
                    $res = S('ABEVipApi')->apiCallEvent($user->openid, $event_hash);
                    $code = $res->code;
                } else {
                    $code = 1;
                }

                if ($code == 1) {
                    S('user')->refreshUserPoint($user);

                    $userReferrer = M('UserReferrer')->getInstance();
                    $userReferrer->user_id = $user->id;
                    $userReferrer->level = $level;
                    $userReferrer->target_id = $target->id;
                    $userReferrer->event_point = $event_point;
                    $userReferrer->save();
                }
            }
        }
    }
}