<?php
namespace app\api\controller\docs;

class Info extends AbstractController
{
    public function index() {
        $domService = S('dom')->init();
        $dom = $domService->readByController($this->request->param('c'), $this->request->param('a'));
        if($dom) {
            $fields = $domService->read('field');
            return $this->fetch('', ['dom'=>$dom, 'fields'=>$fields]);
        }
        return $this->fetch('undefined');
    }

    /**
     * ajax 签名
     * @return string
     */
    public function sign() {
        if(!$this->chkInIpWhite()) // 无权使用在线签名
            return $this->response('', 0, '只能在本地中测试');

        $content = $this->request->getContent();

        $sign = S('ApiAuth')->generateSign($content);
        return $this->response($sign);
    }
}
