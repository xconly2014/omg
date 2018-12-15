<?php

return [
    'controller' => 'app\api\controller\Subject',
    'description' => '',
    'url' => '/api/subject/verdict',
    'param' => [
        'sid' => ['default'=>'', 'desc'=> '主题ID'],
        'values' => ['default'=>'', 'desc'=> '附加问题选择JSON[{"id":1,"value":1},{"id":2,"value":2}]'],
        'result_id' => ['default'=>'', 'desc'=> '结果ID，填写该值。只会返回该ID对应的结果']
    ],
];