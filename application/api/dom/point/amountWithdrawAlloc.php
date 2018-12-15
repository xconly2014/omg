<?php

return [
    'controller' => 'app\api\controller\Point',
    'description' => '',
    'url' => '/api/point/amountWithdrawAlloc',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];