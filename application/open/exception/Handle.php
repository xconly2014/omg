<?php
namespace app\open\exception;

use app\common\exception\ApiException;
use app\common\exception\Factory;
use Exception;
use app\lib\ApiResponse;
use think\exception\Handle As THandle;
use app\lib\Log;
use think\exception\HttpResponseException;
use think\Request;
use traits\controller\Jump;

class Handle extends THandle
{
    use Jump;

    public function render(Exception $e)
    {
        if(!($e instanceof HttpResponseException) ) {
            Log::error('[ req ]'. Request::instance()->getContent());
            Log::error(sprintf("[%s] (%d) %s:%d %s\n", get_class($e), $e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage()));
            Log::error($e->getTraceAsString());
            try {
                $this->error($e->getMessage(), '/open', '', 3);
            } catch (\Exception $e) {
                return $e->getResponse();
            }
        }
    }
}