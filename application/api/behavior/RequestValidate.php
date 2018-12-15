<?php
namespace app\api\behavior;

use app\common\service\Dom;
use think\Request;
use app\common\exception\Factory;
use app\api\controller\ApiController;
use think\Validate;

class RequestValidate
{
    /*
     * 行为 校验接口参数
     */
    public function run(&$action) {
        $request = Request::instance();

        list($controller, $method) = $action;

        if($controller instanceof ApiController) {
            $this->chkRequestMethod($request);

            $validate = $this->getValidator($controller, $method);
            if($validate) {
                $this->chkParam($request, $validate);
            }
        }
    }

    protected function chkParam(Request $request,Validate $validate) {
        if( !$validate->check($request->param()) ) {
            $error = $validate->getError();
            throw Factory::validateError($error);
        }
    }

    /**
     * 生成对应校验器
     * @param $controller
     * @param $method
     * @return null|Validate
     */
    protected function getValidator($controller, $method) {
        if($controller instanceof ApiController) {
            $Dom = new Dom();
            return $Dom->newActionValidator($controller, $method);
        }
        return Null;
    }



    /**
     * 请求方式
     * @param Request $request
     * @throws \app\common\exception\ApiException
     */
    protected function chkRequestMethod(Request $request) {

        if($request->method() != 'POST') {
            throw Factory::invalidRequestMethod();
        }

        $content_type = $request->header('content-type');
        if(strtolower($request->action()) !== 'upload'){
            if(stripos($content_type, 'application/json') === false) {
                throw Factory::invalidRequestMethod();
            }
        }
        
    }
}