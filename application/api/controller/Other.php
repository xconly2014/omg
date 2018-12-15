<?php
namespace app\api\controller;

class Other extends ApiController
{
    /**
     * @title 广告
     * @doc other/ad
     * @validate app\api\service\other\Ad
     */
    public function ad() {
        $data = S('other.ad')->api();
        return $this->response($data);
    }

    /**
     * @title 广告点击事件
     * @doc other/onAdClick
     * @validate app\api\service\other\OnAdClick
     */
    public function onAdClick() {
        $data = S('other.onAdClick')->api();
        return $this->response($data);
    }

    /**
     * @title 全局配置
     * @doc other/config
     * @validate app\api\service\other\Config
     */
    public function config() {
        $data = S('other.config')->api();
        return $this->response($data);
    }
}