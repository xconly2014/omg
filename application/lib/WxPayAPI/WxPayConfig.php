<?php


namespace app\lib\WxPayAPI;

use app\lib\WxPayAPI\lib\WxPayConfigInterface;
use think\Config;

class WxPayConfig extends WxPayConfigInterface
{
    protected $config = [];

    public function __construct()
    {
        $this->config = Config::get('api.wx_mch');
    }

    //=======【基本信息设置】=====================================
    /**
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     */
    public function GetAppId()
    {
        return $this->config['AppID'];
    }
    public function GetMerchantId()
    {
        return $this->config['MchID'];
    }

    //=======【支付相关配置：支付成功回调地址/签名方式】===================================
    /**
     * 支付回调url
     **/
    public function GetNotifyUrl()
    {
        return "";
    }

    /**
     * 签名和验证签名方式， 支持md5和sha256方式
     **/
    public function GetSignType()
    {
        return $this->config['SignType'];
    }

    //=======【curl代理设置】===================================
    /**
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @param $proxyHost
     * @param $proxyPort
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
    }


    //=======【上报信息配置】===================================
    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    public function GetReportLevenl()
    {
        return 1;
    }


    //=======【商户密钥信息-需要业务方继承】===================================
    /*
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    public function GetKey()
    {
        return $this->config['AppKey'];
    }
    public function GetAppSecret()
    {
        return $this->config['AppSecret'];
    }


    //=======【证书路径设置-需要业务方继承】=====================================
    /**
     * 设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     * @param $sslCertPath
     * @param $sslKeyPath
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $sslCertPath = __DIR__ . '/cert/apiclient_cert.pem';
        $sslKeyPath = __DIR__ . '/cert/apiclient_key.pem';
    }
}