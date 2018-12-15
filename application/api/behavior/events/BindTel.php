<?php


namespace app\api\behavior\events;


class BindTel
{
    /**
     * 绑定手机
     * @param $param
     */
    public function run(&$param) {
        $user   = $param['user'];
        if (!$user->is_tel_bind) {
            $event_hash = 'omg_bind_phone';
            $result = false;
            $event = M('activityEvent')->where('hash', $event_hash)->find();
            if ($event) {
                $userEvent = $this->getUserEvent($user, $event);
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
                    $user->is_tel_bind = $result ? 1 : 0;
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