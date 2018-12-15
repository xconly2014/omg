<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => 'https://developers.weixin.qq.com/miniprogram/dev/api/open-api/login/wx.login.html',
    'url' => '/api/wx/onLogin',
    'param' => [
        'code' => ['default'=>'', 'desc'=> '登录时获取的 code'],
        'rouser' => ['default'=>'', 'desc'=> '唤醒我的人openid'],
    ],
];