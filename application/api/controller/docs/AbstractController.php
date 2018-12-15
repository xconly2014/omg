<?php
namespace app\api\controller\docs;


use think\Controller;
use think\exception\HttpException;
use think\Request;
use think\Response;
use think\Lang;


class AbstractController extends Controller
{
    protected $doc_rw;

    public function __construct(Request $request = null)
    {
        if( !config('api.debug') ) {
            throw new HttpException(404, 'Invalid request');
        }
        parent::__construct($request);
    }

    protected function response($data = '', $code = 1, $message= '', $type = 'json', array $header = [])
    {
        !$message && 1 == $code && $message =  Lang::get('E1');
        $result = [
            'code' => $code,
            'message'  => $message,
            'data' => $data,
        ];
        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        return $response;
    }

    protected function chkInIpWhite() {
        return config('api.debug');
        /*$ip = $this->request->ip();
        $num = explode('.', $ip);
        if(!empty($num)) {
            if($num[0] == 127) {
                return true;
            }
            if($num[0] == 192 && $num[1] == 168) {
                return true;
            }
        }
        return false;*/
    }
}