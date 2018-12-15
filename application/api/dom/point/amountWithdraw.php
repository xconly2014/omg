<?php

return [
    'controller' => 'app\api\controller\Point',
    'description' => '',
    'url' => '/api/point/amountWithdraw',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'amount_record_no' => ['default'=>'', 'desc'=> '现金记录单号'],
    ],
];