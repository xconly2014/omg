<?php
namespace app\api\behavior;

use app\lib\Log;
use think\Request;

class WriteApiLog
{
    public function run(&$response) {
        $request = Request::instance();
        if($request->module() == 'api'
            && $request->action() != 'miss'
            && stripos($request->controller(), 'docs.') === false
        ) {
            Log::api($request, $response);
        }
        return true;
    }
}