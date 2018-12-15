<?php

return [
    'controller' => 'app\api\controller\Subject',
    'description' => '',
    'url' => '/api/subject/detail',
    'param' => [
        'sid' => ['default'=>'', 'desc'=> '主题ID'],
        'result_id' => ['default'=>'', 'desc'=> '结果ID，填写该值。会返回该ID对应的结果信息'],
        'openid' => ['default'=>'', 'desc'=> '微信 openid']
    ],
];