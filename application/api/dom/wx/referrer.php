<?php

return [
    'controller' => 'app\api\controller\Wx',
    'description' => '',
    'url' => '/api/wx/referrer',
    'param' => [
        'openid' => ['default'=>'', 'desc'=> '微信 openid'],
        'level' => ['default'=>'1', 'desc'=> '1.一级推荐，2.二级推荐'],
        'page' => ['default'=>'1', 'desc'=> '页码'],
        'page_rows' => ['default'=>'50', 'desc'=> '每页记录数'],
        'page_time' => ['default'=>'', 'desc'=> '第一页的时间戳'],
    ],
];