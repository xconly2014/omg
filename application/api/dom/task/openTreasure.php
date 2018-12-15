<?php

return [
    'controller' => 'app\api\controller\Task',
    'description' => '',
    'url' => '/api/task/openTreasure',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
    ],
];