<?php


namespace app\api\behavior;


use app\api\controller\ApiController;
use app\common\exception\Factory;
use think\Request;

class VerifySign
{
    public function run(&$action) {
        $request = Request::instance();
        list($controller, $method) = $action;

        if($controller instanceof ApiController) {
            $this->chkSign($request);
        }
    }

    /**
     * 验证签名
     * @param Request $request
     * @throws \app\common\exception\ApiException
     */
    protected function chkSign(Request $request) {
        $content = $request->getContent();
        $sign = $request->get('sign');
        if(!$sign) {
            throw Factory::missParam('sign');
        }
        if(strtolower($request->action()) == 'upload'){
            $content = json_encode($request->post());
        }
        if(!$content) {
            throw Factory::missParam('request body');
        }

        $compare = S('apiAuth')->verifySign($content, $sign);
        if(!$compare) {
            throw Factory::invalidRequestSign();
        }
    }
}