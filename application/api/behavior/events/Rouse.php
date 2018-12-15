<?php


namespace app\api\behavior\events;

/**
 * 唤醒用户事件
 * @package app\api\behavior\events
 */
class Rouse
{
    public function run(&$param) {
        $user   = $param['user']; // 唤醒人
        $target = $param['target']; // 被唤醒人
        if ($user && $user->id !== $target->id && S('user')->isSleepy($target)) {
            $event_hash = 'omg_rouse';
            $result = false;
            $event = M('activityEvent')->where('hash', $event_hash)->find();
            if ($event) {
                $userEvent = $this->getUserEvent($user, $event, $target);
                if ($userEvent && $userEvent->collect_at == 0) {
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
                }
            }
        }
    }

    /**
     * 用户唤醒事件
     * @param $user
     * @param $event
     * @param $target
     * @return mixed
     */
    protected function getUserEvent($user, $event, $target) {
        $attach = json_encode(['target_id'=>$target->id]);
        $userEvent = M('userEvent')
            ->where('user_id',$user->id)
            ->where('event_hash', $event->hash)
            ->where('attach', $attach)
            ->find();
        if (!$userEvent) {
            $userEvent = S('activity')->call($user, $event);
        }
        return $userEvent;
    }
}