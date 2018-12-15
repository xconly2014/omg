<?php
namespace app\lib\WxPayAPI;

use app\lib\Log;
use app\lib\WxPayAPI\lib\WxPayBizPayUrl;
use app\lib\WxPayAPI\lib\WxPayApi;
use app\lib\WxPayAPI\lib\WxPayUnifiedOrder;



/**
 *
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay
{
    /**
     * 生成扫描支付URL,模式一
     * @param $productId
     * @return string BizPayUrlInput
     */
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        try{
            $config = new WxPayConfig();
            $values = WxPayApi::bizpayurl($config, $biz);
        } catch(\Exception $e) {
            Log::ERROR(json_encode($e));
        }
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $url;
    }

    /**
     * 参数数组转换为url参数
     * @param array $urlObj
     * @return string
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param WxPayUnifiedOrder $input
     * @return array|bool
     */
    public function GetPayUrl($input)
    {
        if($input->GetTrade_type() == "NATIVE")
        {
            try{
                $config = new WxPayConfig();
                $result = WxPayApi::unifiedOrder($config, $input);
                return $result;
            } catch(\Exception $e) {
                Log::ERROR(json_encode($e));
            }
        }
        return false;
    }
}