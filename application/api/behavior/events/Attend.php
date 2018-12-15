<?php


namespace app\api\behavior\events;

use think\Cache;
use app\common\exception\Factory;

class Attend
{
    public function run(&$param) {
        $user = $param['user'];
        $openid = $user->openid;
        $cacheKey = 'attend:' . $openid;
        if (!Cache::has($cacheKey)) {
            $count = M('UserAttend')->where('openid', $openid)->count();
            $event_hash = $count % 7 === 6 ? 'omg_attend_p10' : 'omg_attend_p2';
            $event = M('activityEvent')->where('hash', $event_hash)->find();
            if ($event) {
                $userEvent = $this->getUserEvent($user, $event);
                if ($userEvent && $userEvent->collect_at == 0) {
                    $result = false;
                    try {
                        $res = S('ABEVipApi')->apiCallEvent($user->openid, $event_hash);
                        $result = $res->code == 1;
                    } catch (\Exception $e) {
                        throw Factory::error('fail to attend');
                    }
                    if ($result) {
                        $userEvent->collect_at = time();
                        $userEvent->save();
                        S('user')->refreshUserPoint($user);
                        $expire = strtotime(date('Y-m-d') . ' +1 day') - time();
                        Cache::set($cacheKey, 1, $expire);
                    }
                }
            }
        }
    }

    protected function getUserEvent($user, $event) {
        $start = strtotime(date('Y-m-d'));
        $end = $start + 86400;
        $userEvent = M('userEvent')
            ->where('user_id',$user->id)
            ->where('event_hash', $event->hash)
            ->whereBetween('create_time', [$start, $end])
            ->find();
        if (!$userEvent) {
            $userEvent = S('activity')->call($user, $event);
        }
        return $userEvent;
    }
}