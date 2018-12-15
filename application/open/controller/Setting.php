<?php

namespace app\open\controller;


class Setting extends AbstractController
{
    public function index() {
        $config = M('Config')->allItem();
        return $this->fetch('', [
            'config' => $config
        ]);
    }

    public function save() {
        $post = $this->request->param();
        $message = $this->validateDataForCreate($post);
        if($message !== true) {
            $this->json(token(), 0, $message);
        }
        $list = [
            ['name' => 'rate-pointToRMB', 'value'=> $post['rate-pointToRMB']],
            ['name' => 'rate-showUpIcon', 'value'=> $post['rate-showUpIcon']],
            ['name' => 'min-withdraw-amount', 'value'=> $post['min-withdraw-amount']],
            ['name' => 'max-withdraw-amount', 'value'=> $post['max-withdraw-amount']]
        ];
        $config = M('Config')->getInstance();
        $config->saveAll($list);
        $this->json(token(), 1);
    }
    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            'rate-showUpIcon' => 'require|in:0,1',
            'rate-pointToRMB' => 'require|number|gt:0',
            'min-withdraw-amount' => 'require|number|egt:100',
            'max-withdraw-amount' => 'require|number'
        ]);
    }
}