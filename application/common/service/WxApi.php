<?php
namespace app\common\service;


use app\common\exception\Factory;
use app\lib\Log;
use think\Cache;
use think\Config;
use think\Exception;
use GuzzleHttp\Client;

class WxApi
{
    protected $appId;

    protected $secret;

    protected $client;

    public function __construct()
    {
        $config = Config::get('api');
        $this->appId = $config['wx']['AppID'];
        $this->secret = $config['wx']['Secret'];

        $this->client = new Client([
            'timeout' => 3.0
        ]);
    }

    /**
     * 微信登录
     * @param $code
     * @return array
     */
    public function jscode2session($code) {
        $query = array(
            'appid' => $this->appId,
            'secret' => $this->secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        );
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $res = $this->httpGet($url, $query);
        return $this->response($res, true);
    }

    /**
     * 获取小程序全局唯一后台接口调用凭据
     * @return string
     * @throws \app\common\exception\LangException
     */
    public function getAccessToken() {
        $cacheKey = 'WxApi:AccessToken';
        $access_token = Cache::get($cacheKey);
        if (!$access_token) {
            $config = Config::get('api.wx');
            $query = array(
                'grant_type' => 'client_credential',
                'appid' => $config['AppID'],
                'secret' => $config['Secret']
            );
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $res = $this->httpGet($url, $query);
            $res = $this->response($res);
            if (isset($res->access_token) && $res->access_token) {
                $access_token = $res->access_token;
                Cache::set($cacheKey, $access_token, $res->expires_in - 60);
            } else {
                throw Factory::error('fail to get AccessToken');
            }
        }
        return $access_token;
    }

    /**
     * 小程序二维码
     * https://developers.weixin.qq.com/miniprogram/dev/api/getWXACodeUnlimit.html
     * @param array $param
     * @param $access_token
     * @return null
     */
    public function getWXACodeUnlimit(array $param, $access_token = false) {
        if (isset($param['scene'])) {
            $access_token || $access_token = $this->getAccessToken();
            if ($access_token) {
                $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;
                $res = $this->httpJson($url, $param);
                return $this->responseFile($res);
            }
        }
        return null;
    }

    /**
     * GET
     * @param $url
     * @param $query
     * @return mixed
     */
    protected function httpGet($url, $query) {
        Log::message('[WxApi] ' . $url);
        return $this->client->request('GET', $url, [
            'verify'  =>  false,
            'query' => $query
        ]);
    }

    /**
     * POST json
     * @param $url
     * @param $data
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function httpJson($url, $data) {
        Log::message('[WxApi] ' . $url);
        Log::message('[WxApi] [REQ] ' . json_encode($data, JSON_UNESCAPED_UNICODE));

        return $this->client->request('POST', $url, [
            'json' => $data,
            'verify'  =>  false,
        ]);
    }

    /**
     * 响应
     * @param $res
     * @param $assoc
     * @return mixed
     * @throws \app\common\exception\LangException
     */
    protected function response($res, $assoc = false) {
        if ($res->getStatusCode() !== 200) {
            Log::message( '[WxApi] [ERR] ' . $res->getStatusCode() );
            throw Factory::error('WxApiHttpErrCode:' . $res->getStatusCode());
        }
        $content = $res->getBody()->getContents();
        Log::message( '[WxApi] [RESP]' . $content );
        $json = \GuzzleHttp\json_decode($content, $assoc);
        if (isset($json->errcode) && $json->errcode) {
            throw Factory::error('WxApiErr:' . $json->errcode);
        }
        return $json;
    }

    /**
     * 响应文件
     * @param $res
     * @return mixed
     * @throws \app\common\exception\LangException
     */
    protected function responseFile($res) {
        if ($res->getStatusCode() !== 200) {
            Log::message( '[WxApi] [ERR] ' . $res->getStatusCode() );
            throw Factory::error('WxApiHttpErrCode:' . $res->getStatusCode());
        }
        $content = $res->getBody()->getContents();
        Log::message( '[WxApi] [RESP]' . $content );
        if (strpos(ltrim($content), '{') === 0) {
            $json = \GuzzleHttp\json_decode($content);
            if (isset($json->errcode) && $json->errcode) {
                throw Factory::error('WxApiErr:' . $json->errcode);
            }
            return $json;
        }
        return $content;
    }
}