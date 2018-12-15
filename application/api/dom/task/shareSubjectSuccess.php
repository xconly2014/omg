<?php

return [
    'controller' => 'app\api\controller\Task',
    'description' => '',
    'url' => '/api/task/shareSubjectSuccess',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
    ],
];