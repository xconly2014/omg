<?php


namespace app\common\service;


use app\common\exception\Factory;
use think\Config;
use app\lib\WxPayAPI\MmTransfers;

class Withdraw
{
    /**
     * 发送提现申请到微信支付
     * @param $openid
     * @param $withdraw
     * @return mixed
     * @throws \app\common\exception\LangException
     */
    public function sendToWx($openid, $withdraw) {
        $trade_no = $withdraw->trade_no;
        $amount = $withdraw->amount;

        if ($withdraw->transfer_status != 1) {
            $transfers = new MmTransfers();
            $resp = $transfers->send([
                'partner_trade_no' => $trade_no,
                'openid' => $openid,
                'amount' => $amount,
                'desc'   => '666偶滴神啊积分提现'
            ]);

            if ($resp['result_code'] === 'SUCCESS') {
                $withdraw->transfer_status = 1;
                $withdraw->transfer_no = $resp['payment_no'];
                $withdraw->transfer_at = strtotime($resp['payment_time']);
                $withdraw->save();
                $this->changePoint();
            } else {
                $withdraw->transfer_status = 100;
                $withdraw->transfer_err = $resp['err_code'] . ':' . $resp['err_code_des'];
                $withdraw->save();
                throw Factory::error($withdraw->transfer_err);
            }
        }
        return $withdraw;
    }

    /**
     * 给所有提现成功的openid 扣减积分
     */
    public function changePoint() {
        $config = Config::get('api.wx');
        $rows = M('withdraw')->where('transfer_status', 1)->where('is_point_change', 0)->select();
        if ($rows && !empty($rows)) {
            try {
                foreach ($rows as $row) {
                    $res = S('ABEVipApi')->send('tp/wxSubtractPoint', [
                        'appID' => $config['AppID'],
                        'openid' => $row->openid,
                        'trade_no' => $row->trade_no,
                        'point' => $row->point
                    ]);
                    if ($res) {
                        $row->is_point_change = 1;
                        $row->save();
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function simpleInfo(\app\common\model\Withdraw $withdraw) {
        return [
            'trade_type' => $withdraw->trade_type,
            'trade_no' => $withdraw->trade_no,
            'point' => $withdraw->point,
            'amount' => $withdraw->amount,
            'transfer_status' => $withdraw->transfer_status,
            'transfer_at' => $withdraw->transfer_at,
            'transfer_no' => $withdraw->transfer_no,
            'transfer_err' => $withdraw->transfer_err,
        ];
    }
}