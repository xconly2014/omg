<?php

return [
    'controller' => 'app\api\controller\Subject',
    'description' => 'page_time:第一页的时间戳。从第二页开始，每次传的值要与第一页相同',
    'url' => '/api/subject/Lister',
    'param' => [
        'page' => ['default'=>'1', 'desc'=> '页码'],
        'page_rows' => ['default'=>'20', 'desc'=> '每页记录数'],
        'page_time' => ['default'=>'', 'desc'=> '第一页的时间戳'],
        'sort' => ['default'=>'', 'desc'=> '排序 0.默认，1.热度'],
        'filter' => ['default'=>'', 'desc'=> '过滤SID'],
        'debug' => ['default'=>'', 'desc'=> '是否测试，测试将显示未发布主题']
    ],
];