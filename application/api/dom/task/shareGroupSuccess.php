<?php

return [
    'controller' => 'app\api\controller\Task',
    'description' => '',
    'url' => '/api/task/shareGroupSuccess',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'openGId' => ['default'=>'', 'desc'=> '微信 群ID'],
        'formId' => ['default'=>'', 'desc'=> '微信表单ID'],
    ],
];