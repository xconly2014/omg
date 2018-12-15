<?php

return [
    'controller' => 'app\api\controller\task',
    'description' => '',
    'url' => '/api/task/enter',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];