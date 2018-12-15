<?php

return [
    'controller' => 'app\api\controller\Other',
    'description' => '',
    'url' => '/api/other/onAdClick',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'ad_id' => ['default'=>'ad_001', 'desc'=> 'ad_001']
    ],
];