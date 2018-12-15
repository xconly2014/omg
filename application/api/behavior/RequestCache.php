<?php
namespace app\api\behavior;

use app\lib\ApiResponse;
use think\Cache;
use think\exception\HttpResponseException;
use think\Request;

/**
 * Class RequestCache
 * API请求和响应缓存
 * @package app\api\behavior
 */
class RequestCache
{
    public function actionBegin(&$action) {
        $key = $this->caleKey();
        if(Cache::has($key)) {
            $response = Cache::get($key);
            throw new HttpResponseException($response);
        }
    }

    public function responseSend(&$response) {
        $key = $this->caleKey();
        if( !Cache::has($key) ) {
            if($response instanceof ApiResponse) {
                $expire = $response->getCacheExpire();
                if(!is_null($expire)) {
                    Cache::set($key, $response, $expire);
                }
            }
        }
    }

    protected function caleKey() {
        $request = Request::instance();
        $path = $request->controller().':'.$request->action();
        $params = $request->param();
        if( isset($params['sign']) ) { unset($params['sign']); }
        if( isset($params['timestamp']) ) { unset($params['timestamp']); }
        if( isset($params['page_time']) ) {
            $params['page_time'] -= $params['page_time'] % 600;
        }
        $key = 'ApiResp:'.$path.md5(json_encode($params));
        return $key;
    }
}