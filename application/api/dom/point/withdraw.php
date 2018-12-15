<?php

return [
    'controller' => 'app\api\controller\Point',
    'description' => '',
    'url' => '/api/point/withdraw',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'amount' => ['default'=>'', 'desc'=> '提现金额，单位为分的整数'],
    ],
];