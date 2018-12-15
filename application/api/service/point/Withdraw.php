<?php


namespace app\api\service\point;


use app\api\service\AbstractApiHandler;
use app\common\exception\Factory;


/**
 * 积分提现
 * Class Withdraw
 * @package app\api\service\point
 */
class Withdraw extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
        'amount'    => 'require|integer|egt:100|elt:10000',
    ];

    public function __construct()
    {
        $this->message('amount.egt', '输入值必须大于等于1');
        $this->message('amount.elt', '输入值超出限制额度');
        parent::__construct();
    }

    public function api() {
        $openid = $this->param('openid');
        $user = S('user')->getUser($openid);
        $this->chkUnionID($user);
        $userPoint = S('user')->refreshUserPoint($user);
        $withdraw  = $this->createWithdrawRow($openid, $userPoint);
        $this->withdrawFromWx($openid, $withdraw);
        return S('withdraw')->simpleInfo($withdraw);
    }

    protected function chkUnionID($user) {
        if (!$user->unionid) {
            throw Factory::invalidToken('请先关注我们的公众号：海创游戏');
        }
    }

    /**
     * 新建提现订单记录
     * @param $openid
     * @param $cur_point
     * @return mixed
     * @throws \app\common\exception\LangException
     */
    protected function createWithdrawRow($openid, $cur_point) {
        $rate = M('config')->getItem('rate-pointToRMB');
        if (!$rate) {
            throw Factory::error('无效兑换比例');
        }
        $amount = $this->param('amount');
        $min = M('config')->getItem('mix-withdraw-amount');
        $max = M('config')->getItem('max-withdraw-amount');
        $used = $this->usedAmount($openid);
        $min || $min = 1;
        $max || $max = 1;
        $max -= $used;

        $cur_amount = (int) bcdiv($cur_point, $rate); // 单位: 分
        if($cur_amount < $amount) {
            throw Factory::error('积分不足');
        }
        $point = bcmul($amount, $rate, 4);

        if ($max <= 0) {
            throw Factory::error('提现额度已用完');
        }
        if ($amount < $min) {
            throw Factory::error('金额不足');
        }
        if ($amount > $max) {
            $amount = $max;
        }

        $withdraw = M('withdraw')->getInstance();
        $withdraw->openid = $openid;
        $withdraw->point = $point;
        $withdraw->amount = $amount;
        $withdraw->trade_type = 'POINT';
        $withdraw->transfer_status = 0;
        $withdraw->save();
        return $withdraw;
    }

    protected function usedAmount($openid) {
        return M('withdraw')
            ->where('openid', $openid)
            ->where('transfer_status', '<=', 1)
            ->where('trade_type', 'POINT')
            ->sum('amount');
    }

    /**
     * 发起微信企付到用户零钱
     * @param $openid
     * @param $withdraw
     * @return array|bool|null
     */
    protected function withdrawFromWx($openid, $withdraw) {
        $auto = M('Config')->getItem('withdraw_autoSend');
        if ($auto) {
            return S('withdraw')->sendToWx($openid, $withdraw);
        }
        return null;
    }
}