<?php

return [
    'controller' => 'app\api\controller\Task',
    'description' => '',
    'url' => '/api/task/contactUsSuccess',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
    ],
];