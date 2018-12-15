<?php

return [
    'controller' => 'app\api\controller\task',
    'description' => '',
    'url' => '/api/task/referrer',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];