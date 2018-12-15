<?php


namespace app\api\behavior\events;


class FollowOur
{
    /**
     * 关注公众号
     * @param $param
     */
    public function run(&$param) {
        $user   = $param['user'];
        if ($user->unionid && !$user->is_follow_our) {
            $event_hash = 'omg_follow';
            $result = false;
            $event = M('activityEvent')->where('hash', $event_hash)->find();
            if ($event) {
                $userEvent = $this->getUserEvent($user, $event);
                if ($userEvent && $userEvent->collect_at == 0 ) {
                    try {
                        $res = S('ABEVipApi')->apiCallEvent($user->openid, $event_hash);
                        $result = $res->code == 1;
                    } catch (\Exception $e) {
                    }
                    if ($result) {
                        S('user')->refreshUserPoint($user);
                        $userEvent->collect_at = time();
                        $userEvent->save();
                    }
                    $user->is_follow_our = $result ? 1 : 0;
                    $user->save();
                }
            }
        }
    }

    protected function getUserEvent($user, $event) {
        $userEvent = M('userEvent')
            ->where('user_id',$user->id)
            ->where('event_hash', $event->hash)
            ->find();
        if (!$userEvent) {
            $userEvent = S('activity')->call($user, $event);
        }
        return $userEvent;
    }
}