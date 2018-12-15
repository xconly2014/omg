<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => '',
    'url' => '/api/wx/attend',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];