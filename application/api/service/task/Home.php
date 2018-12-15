<?php
namespace app\api\service\task;

use app\api\service\AbstractApiHandler;
use think\Cache;

/**
 * 签到
 * @package app\api\service\wx
 */
class Home extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64'
    ];

    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        $data = $this->pointInfo($user);

        # 显示分享群成功
        $data['is_after_share_group'] = Cache::get('is_after_share_group:' . $user->id);
        $data['is_after_share_group'] || $data['is_after_share_group'] = 0;

        $data['note'] = '联系客服了解积分提现相关信息';
        $data['note_modal'] = '换算比例';
        $data['note_modal_body'] = '每天的广告收入会按照一定比例返回给用户，所以如果广告收入越多积分能兑换的红包金额越高!';
        # ？积分起提现
        $min_withdraw_amount = M('config')->getItem('min-withdraw-amount');
        $rate_pointToRMB = M('config')->getItem('rate-pointToRMB');
        if ($min_withdraw_amount && $rate_pointToRMB) {
            $data['withdraw_minPoint'] = $min_withdraw_amount * $rate_pointToRMB;
        } else {
            $data['withdraw_minPoint'] = 200;
        }

        $data['task'] = S('activity')->groupListInfo($user);
        return $data;
    }

    /**
     * 积分详情
     * @param $user
     * @return array
     */
    protected function pointInfo($user) {
        $point = S('user')->getUserPoint($user);

        $data = S('user')->simpleInfo($user);
        $data['point'] = $point;
        $data['amount'] = $this->rateToRMB($point);
        $data['rate_rmb_1000'] = $this->rateToRMB(1000);
        return $data;
    }

    /**
     * 积分转？元
     * @param int $point
     * @return string
     */
    protected function rateToRMB($point = 1000) {
        $rate = M('config')->getItem('rate-pointToRMB');
        $amount = (int) bcdiv($point, $rate);
        return number_format($amount / 100, 2, '.', '');
    }
}