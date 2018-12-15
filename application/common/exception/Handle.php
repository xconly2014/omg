<?php
namespace app\common\exception;

use Exception;
use app\lib\ApiResponse;
use think\exception\Handle As THandle;
use app\lib\Log;
use think\Request;

class Handle extends THandle
{
    public function render(Exception $e)
    {
        $is_api_exception = $e instanceof ApiException;
        if(!$is_api_exception ) {
            if(config('app_debug')){
                return parent::render($e);
            }
            Log::error('[ req ]'. Request::instance()->getContent());
            //Log::error(sprintf("[%s] (%d) %s:%d %s\n", get_class($e), $e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage()));
            Log::error($e->getTraceAsString());
            $e = Factory::sysError($e);
        }
        $response = new ApiResponse();
        $response->error($e);
        //if($is_api_exception) {
            Log::api(Request::instance(), $response);
        //}
        return $response;
    }
}