<?php


namespace app\api\behavior\events;


class OpenTreasure
{
    /**
     * 开启宝箱
     * @param $param
     */
    public function run(&$param) {
        $user   = $param['user'];
        $event_hash = $this->randomEventHash();
        $param['result'] = false;
        $event = M('activityEvent')->where('hash', $event_hash)->find();
        if ($event) {
            $userEvent = $this->getUserEvent($user, $event);
            if ($userEvent && $userEvent->collect_at == 0 ) {
                try {
                    $res = S('ABEVipApi')->apiCallEvent($user->openid, $event_hash);
                    $param['result'] = $res->code == 1;
                } catch (\Exception $e) {
                }
                if ($param['result']) {
                    S('user')->refreshUserPoint($user);
                    $userEvent->collect_at = time();
                    $userEvent->save();
                }
                $param['userEvent'] = $userEvent;
            }
        }
        return $param;
    }

    protected function randomEventHash() {
        $hash = 'omg_treasure_1';
        $num = mt_rand(1, 10000);
        if ($num <= 1) {
            $hash = 'omg_treasure_7';
        } elseif ($num <= 10) {
            $hash = 'omg_treasure_6';
        } elseif ($num <= 50) {
            $hash = 'omg_treasure_5';
        } elseif ($num <= 100) {
            $hash = 'omg_treasure_4';
        } elseif ($num <= 200) {
            $hash = 'omg_treasure_3';
        } elseif ($num <= 500) {
            $hash = 'omg_treasure_2';
        } elseif ($num <= 1000) {
            $hash = 'omg_treasure_1';
        }
        return $hash;
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