<?php


namespace app\api\service\point;


use app\api\service\AbstractApiHandler;

/**
 * 积分提现信息
 * Class WithdrawAlloc
 * @package app\api\service\point
 */
class WithdrawAlloc extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
    ];

    public function api()
    {
        $openid = $this->param('openid');
        $user = S('user')->getUser($openid);
        $user_point = S('user')->refreshUserPoint($user);
        $rate = M('config')->getItem('rate-pointToRMB');
        $min = (int)M('config')->getItem('min-withdraw-amount');
        $max = (int)M('config')->getItem('max-withdraw-amount');
        $min || $min = 1;
        $max || $max = 1;
        $used = $this->usedAmount($openid);
        $user_amount = (int)bcdiv($user_point, $rate);
        $operable = $max - $used;
        $operable = min($user_amount, $operable);

        if ($operable < $min) {
            $operable = 0;
        }
        $operable = floor($operable / 100) * 100;
        $rate_rmb_1000 = $this->rateToRMB(1000, $rate);

        return [
            'rate' => $rate,
            'user_point' => $user_point,
            'user_amount' => $user_amount,
            'min_amount' => $min,
            'max_amount' => $max,
            'used_amount' => $used,
            'operable_amount' => $operable,
            'rate_rmb_1000' => $rate_rmb_1000,
            'note' => '提现申请将在1个工作日内审批到账；如果遇到高峰期，可能延迟到账，麻烦耐心等待，如意疑问请联系客服',
        ];
    }

    protected function usedAmount($openid) {
        return M('withdraw')
            ->where('openid', $openid)
            ->where('transfer_status', '<=', 1)
            ->where('trade_type', 'POINT')
            ->sum('amount');
    }

    /**
     * 积分转？元
     * @param int $point
     * @param int $rate
     * @return string
     */
    protected function rateToRMB($point = 1000, $rate = null) {
        $rate || $rate = M('config')->getItem('rate-pointToRMB');
        $amount = (int) bcdiv($point, $rate);
        return number_format($amount / 100, 2, '.', '');
    }
}