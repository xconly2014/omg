<?php


namespace app\common\service;

use app\common\exception\Factory;
use app\lib\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;

use think\Config;
use think\Exception;

class ABEVipApi
{
    protected $apiSecret;

    protected $client;

    public function __construct()
    {
        $config = Config::get('api.abe_vip');
        $this->apiSecret = $config['secret'];
        $this->client = new Client([
            'base_uri' => $config['base_uri'],
            'timeout' => 6.0
        ]);
    }

    /**
     * 发送请求
     * @param $api
     * @param $data
     * @return mixed
     */
    public function send($api, $data) {
        if (!isset($data['timestamp'])) $data['timestamp'] = time();
        if (!isset($data['version'])) $data['version'] = 1;

        Log::message( '[ABE] ' . $api );
        Log::message( '[ABE] [data] ' . json_encode($data, JSON_UNESCAPED_UNICODE) );
        $res = $this->client->request('POST', $api, [
            'json' => $data,
            'handler' => $this->handler()
        ]);
        return $this->response($res);
    }

    public function asyncSend($api, $data) {
        if (!isset($data['timestamp'])) $data['timestamp'] = time();
        if (!isset($data['version'])) $data['version'] = 1;

        return $this->client->requestAsync('POST', $api, [
            'json' => $data,
            'handler' => $this->handler()
        ]);
    }

    /**
     * 微信- 触发事件
     * @param $openid
     * @param $hash
     * @return mixed
     */
    public function apiCallEvent($openid, $hash) {
        $config = Config::get('api.wx');
        $data['appID'] = $config['AppID'];
        $data['openid'] = $openid;
        $data['event_hash'] = $hash;

        return S('ABEVipApi')->send('tp/wxCallEvent', $data);
    }

    protected function response($res) {
        if ($res->getStatusCode() !== 200) {
            Log::message( '[ABE] [err] ' . $res->getStatusCode() );
            throw new Exception('apiErrCode:' . $res->getStatusCode());
        }
        $content = $res->getBody()->getContents();
        $json = \GuzzleHttp\json_decode($content);
        if ($json->code !== 1) {
            throw Factory::error('apiErr:' . $json->message, $json->code);
        }
        Log::message( '[ABE] [resp]' . $content );
        return $json;
    }

    protected function handler() {
        $clientHandler = $this->client->getConfig('handler');
        // Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::mapRequest(function ($request) {
            $config = Config::get('api.abe_vip');
            $body = $request->getBody();
            $sign = S('apiAuth')->sign($config['secret'], $body);
            $uri = $request->getUri()->withQuery('sign=' . $sign);
            return $request->withUri($uri);
        });
        return $tapMiddleware($clientHandler);
    }
}