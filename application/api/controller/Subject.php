<?php
namespace app\api\controller;

class Subject extends ApiController
{
    /**
     * @title 列表
     * @doc subject/lister
     * @validate app\api\service\subject\Lister
     */
    public function lister() {
        $data = S('subject.lister')->api();
        return $this->response($data);
    }

    /**
     * @title 详情
     * @doc subject/detail
     * @validate app\api\service\subject\Detail
     */
    public function detail() {
        $data = S('subject.detail')->api();
        return $this->response($data)->cache('M');
    }

    /**
     * @title 结论
     * @doc subject/verdict
     * @validate app\api\service\subject\Verdict
     */
    public function verdict() {
        $data = S('subject.verdict')->api();
        return $this->response($data);
    }
}
