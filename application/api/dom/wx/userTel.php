<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => '',
    'url' => '/api/wx/userTel',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'tel_prefix' => ['default'=>'86', 'desc'=> '手机区号, 不填默认使用86'],
        'tel_number' => ['default'=>'', 'desc'=> '手机号码'],
        'auth' => ['default'=>'', 'desc'=> '手机验证码']
    ],
];