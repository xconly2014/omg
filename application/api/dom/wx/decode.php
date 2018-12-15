<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => '',
    'url' => '/api/wx/decode',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'encryptedData' => ['default'=>'', 'desc'=> '密文内容'],
        'iv' => ['default'=>'', 'desc'=> '参考向量']
    ],
];