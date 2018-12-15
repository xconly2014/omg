<?php


namespace app\lib\sms;


use app\lib\Log;
use think\Config;

class Aliyun
{
    protected $AccessKeyId = 'fHmYVJWlxu8MAugZ';
    protected $AccessKeySecret = 'uFssD1kWGnC54PYx6C3E6Ej4ny0dkD';

    protected $domain = 'dysmsapi.aliyuncs.com';

    protected $params = [
        "RegionId" => "cn-hangzhou",
        "Action" => "SendSms",
        "Version" => "2017-05-25",
    ];

    protected $tpl_map = [
        //'user_auth' => 'SMS_71161067',
        'user_auth' => 'SMS_139115057',
    ];

    protected $helper;

    public function __construct()
    {
        $conf = Config::get('api.ali_sms');
        $this->AccessKeyId = $conf['AccessKeyId'];
        $this->AccessKeySecret = $conf['AccessKeySecret'];

        $this->helper = new SignatureHelper();
        $this->signName('ABE');
        $this->templateParam('product', 'ABE');
    }

    public function to($phone_number) {
        $this->param('PhoneNumbers', $phone_number);
        return $this;
    }

    public function signName($sign) {
        $this->param('SignName', $sign);
        return $this;
    }

    public function template($template_code) {
        isset($this->tpl_map[ $template_code ]) && $template_code = $this->tpl_map[ $template_code ];
        $this->param('TemplateCode', $template_code);
        return $this;
    }

    public function templateParam($param, $value = null) {
        if(is_string($param)) {
            $params = $this->getParams();
            $template_param = !empty($params['TemplateParam'])  ? $params['TemplateParam'] : [];
            $template_param[ $param ] = $value;
        } else {
            $template_param = $param;
        }
        $this->param('TemplateParam', $template_param);
        return $this;
    }

    public function send() {
        $params = $this->getParams();
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        return $this->helper->request(
            $this->AccessKeyId,
            $this->AccessKeySecret,
            $this->domain,
            $params
        );
    }

    /**
     * @param $data
     * @param null $value
     * @return $this
     */
    public function param($data, $value = null)
    {
        if (is_array($data)) {
            $this->params = $data;
        } else {
            $this->params[$data] = $value;
        }
        return $this;
    }

    public function getParams() {
        return $this->params;
    }
}

/**
 * 签名助手 2017/11/19
 *
 * Class SignatureHelper
 */
class SignatureHelper {

    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security=false) {
        $apiParams = array_merge(array (
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));
        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&",true));
        $signature = $this->encode($sign);
        $url = ($security ? 'https' : 'http')."://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

        $this->writeLog('[url] ' . $url);

        try {
            $content = $this->fetchContent($url);
            $result = json_decode($content);
            $this->writeLog('[resp] ' . $content);
        } catch( \Exception $e) {
            $this->writeLog('[err] ' . $e->getMessage());
            $result = false;
        }
        return $result;
    }

    protected function writeLog( $message ) {
        Log::message('[ali_sms] ' . $message);
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }


}