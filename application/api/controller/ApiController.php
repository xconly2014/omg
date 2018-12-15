<?php
namespace app\api\controller;

use think\Controller;
use app\lib\ApiResponse;


class ApiController extends Controller
{
    public function response($data = [])
    {
        if(gettype($data) == 'object') {
            $data = get_object_vars($data);
        }
        $response = new ApiResponse();
        $response->success($data);
        return $response;
    }
}