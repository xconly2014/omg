<?php


namespace app\open\controller;


class Upload extends AbstractController
{
    public function image() {
        $result = S('upload')->upload();
        $url = isset($result['data']['file']) ? $result['data']['file'] : '';
        $this->result($url, $result['code'], $result['msg']);
    }
}