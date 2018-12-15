<?php


namespace app\api\service\point;


use app\api\service\AbstractApiHandler;

/**
 * 任务-邀请提现信息
 * Class AmountWithdrawAlloc
 * @package app\api\service\point
 */
class AmountWithdrawAlloc extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
    ];

    public function api()
    {
        $openid = $this->param('openid');
        $user = S('user')->getUser($openid);
        $data['stall'] = $this->fetchStallList($user);
        $data['note'] = '提现申请将在1个工作日内审批到账；如果遇到高峰期，可能延迟到账，麻烦耐心等待，如意疑问请联系客服';
        return $data;
    }

    protected function fetchStallList($user) {
        $list = [];
        $activity = M('activity')->findById(1);
        $events = $activity->events;
        if (!empty($events)) {
            foreach ($events as $event) {
                $item['key'] = $event->hash;
                $item['amount'] = $event->amount;
                $item['is_finished'] = 0;
                $item['is_withdrawable'] = 0;
                $item['amount_record_no'] = '';

                $record = M('userEvent')->findByUserIDAndHash($user->id, $event->hash);
                if ($record) {
                    if ( $record->collect_at) {
                        $item['is_finished'] = 1;
                    } else {
                        $item['is_withdrawable'] = 1;
                        $item['amount_record_no'] = $record->record_no;
                    }
                }
                $list[] = $item;
            }
        }
        return $list;
    }
}