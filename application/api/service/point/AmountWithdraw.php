<?php


namespace app\api\service\point;


use app\api\service\AbstractApiHandler;
use app\common\exception\Factory;


/**
 * 现金提现
 * Class Withdraw
 * @package app\api\service\point
 */
class AmountWithdraw extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
        'amount_record_no'    => 'require|alphaDash|length:20',
    ];

    public function api() {
        $openid = $this->param('openid');
        $user = S('user')->getUser($openid);
        $this->chkUnionID($user);
        $userEvent = S('userEvent')->getByRecordNo($this->param('amount_record_no'));
        $withdraw  = $this->createWithdrawRow($openid, $userEvent);
        $this->withdrawFromWx($openid, $withdraw, $userEvent);
        return S('withdraw')->simpleInfo($withdraw);
    }

    protected function chkUnionID($user) {
        if (!$user->unionid) {
            throw Factory::invalidToken('请先关注我们的公众号：666偶滴神');
        }
    }

    protected function createWithdrawRow($openid, $userEvent) {
        if ($userEvent->withdraw_id) {
            $withdraw = M('withdraw')->findById($userEvent->withdraw_id);
        } else {
            $withdraw = M('withdraw')->getInstance();
            $withdraw->openid = $openid;
            $withdraw->amount = $userEvent->amount;
            $withdraw->trade_type = 'AMOUNT';
            $withdraw->transfer_status = 0;
            $withdraw->save();

            $userEvent->collect_at = time();
            $userEvent->withdraw_id = $withdraw->id;
            $userEvent->save();
        }

        return $withdraw;
    }

    /**
     * 发起微信企付到用户零钱
     * @param $openid
     * @param $withdraw
     * @param $userEvent
     * @return array|bool|null
     */
    protected function withdrawFromWx($openid, $withdraw, $userEvent) {
        $auto_withdraw = M('activityEvent')->where('activity_id', $userEvent->activity_id)->where('hash', $userEvent->event_hash)->value('auto_withdraw');
        if ($auto_withdraw) {
            return S('withdraw')->sendToWx($openid, $withdraw);
        }
        return null;
    }
}