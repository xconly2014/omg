<?php


namespace app\lib\WxPayAPI;

use app\lib\WxPayAPI\lib\MmPayApi;
use app\lib\WxPayAPI\lib\MmPayTransfers;


class MmTransfers
{
    public function send($data) {

        $config = new WxPayConfig();
        $inputObj = new MmPayTransfers();

        $inputObj->SetAmount($data['amount']); // 单位： 分
        $inputObj->SetPartner_trade_no($data['partner_trade_no']);
        $inputObj->SetOpenid($data['openid']);

        isset($data['device_info']) && $inputObj->SetDevice_info($data['device_info']);
        isset($data['re_user_name']) && $inputObj->SetRe_user_name($data['re_user_name']);
        isset($data['check_name']) || $data['check_name'] = 'NO_CHECK';
        isset($data['desc']) || $data['desc'] = '';

        $inputObj->SetCheck_name($data['check_name']);
        $inputObj->SetDesc($data['desc']);

        return MmPayApi::transfers($config, $inputObj);
    }
}