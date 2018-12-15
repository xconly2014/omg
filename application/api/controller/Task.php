<?php
namespace app\api\controller;

class Task extends ApiController
{
    /**
     * @title 首次进入
     * @doc task/enter
     * @validate app\api\service\task\Enter
     */
    public function enter() {
        $data = S('task.enter')->api();
        return $this->response($data);
    }

    /**
     * @title 任务中心
     * @doc task/home
     * @validate app\api\service\task\Home
     */
    public function home() {
        $data = S('task.home')->api();
        return $this->response($data);
    }

    /**
     * @title 我的邀请[任务详情]
     * @doc task/referrer
     * @validate app\api\service\task\Referrer
     */
    public function referrer() {
        $data = S('task.referrer')->api();
        return $this->response($data);
    }

    /**
     * @title [任务] - 转发群成功
     * @doc task/shareGroupSuccess
     * @validate app\api\service\task\ShareGroupSuccess
     */
    public function shareGroupSuccess() {
        $data = S('task.shareGroupSuccess')->api();
        return $this->response($data);
    }

    /**
     * @title [任务] - 联系客服成功
     * @doc task/contactUsSuccess
     * @validate app\api\service\task\ContactUsSuccess
     */
    public function contactUsSuccess() {
        $data = S('task.contactUsSuccess')->api();
        return $this->response($data);
    }

    /**
     * @title [任务] - 看题目分享成功
     * @doc task/shareSubjectSuccess
     * @validate app\api\service\task\ShareSubjectSuccess
     */
    public function shareSubjectSuccess() {
        $data = S('task.shareSubjectSuccess')->api();
        return $this->response($data);
    }

    /**
     * @title [任务] - 开启宝箱
     * @doc task/openTreasure
     * @validate app\api\service\task\OpenTreasure
     */
    public function openTreasure() {
        $data = S('task.openTreasure')->api();
        return $this->response($data);
    }
}