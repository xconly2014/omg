<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => '',
    'url' => '/api/wx/userInfo',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'unionID' => ['default'=>'', 'desc'=> '微信 unionId'],
        'nickname' => ['default'=>'', 'desc'=> '昵称'],
        'avatar' => ['default'=>'', 'desc'=> '头像URL'],
        'gender' => ['default'=>'', 'desc'=> '性别 0：未知、1：男、2：女'],
        'province' => ['default'=>'', 'desc'=> '省'],
        'city' => ['default'=>'', 'desc'=> '市'],
        'country' => ['default'=>'', 'desc'=> '国'],
        'referrer' => ['default'=>'', 'desc'=> '引荐人，openID'],
    ],
];