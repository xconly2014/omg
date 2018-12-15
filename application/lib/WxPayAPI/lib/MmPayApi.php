<?php


namespace app\lib\WxPayAPI\lib;


use app\lib\Log;
use app\lib\WxPayAPI\WxPayConfig;

class MmPayApi
{
    /**
     * 企业付款
     * https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2
     * @param WxPayConfig $config
     * @param MmPayTransfers $inputObj
     * @param int $timeOut
     * @return array|bool
     * @throws WxPayException
     */
    public static function transfers($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        //检测必填参数
        if(!$inputObj->IsPartner_trade_noSet()) {
            throw new WxPayException("缺少企业付款必填参数partner_trade_no！");
        }else if(!$inputObj->IsOpenidSet()) {
            throw new WxPayException("缺少企业付款必填参数openid！");
        }else if(!$inputObj->IsAmountSet()) {
            throw new WxPayException("缺少企业付款必填参数amount！");
        }

        $inputObj->SetMch_appid($config->GetAppId());
        $inputObj->SetMchid($config->GetMerchantId());
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        $inputObj->SetNonce_str(WxPayApi::getNonceStr());//随机字符串

        //签名
        $inputObj->SetSign($config);
        $xml = $inputObj->ToXml();

        Log::message('[MMPay][REQ]' . json_encode($inputObj->GetValues(), JSON_UNESCAPED_UNICODE));
        //$startTimeStamp = WxPayApi::getMillisecond();//请求开始时间
        $response = WxPayApi::postXmlCurl($config, $xml, $url, true, $timeOut);
        $result = WxPayResults::Init($config, $response, false);
        Log::message('[MMPay][RESP]' . json_encode($result, JSON_UNESCAPED_UNICODE));
        //WxPayApi::reportCostTime($config, $url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }
}