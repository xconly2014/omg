<?php

return [
    'controller' => 'app\api\controller\task',
    'description' => '',
    'url' => '/api/task/home',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];