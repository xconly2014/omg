<?php
return [
    // URL普通方式参数 用于自动生成
    'url_common_param'       => true,

    //全局公共请求参数，设置了所以的接口会自动增加次参数
    'common_param' => [
        'timestamp' => ['default'=>time(), 'desc'=> '秒数时间戳'],
        'version' => ['default'=>'1', 'desc'=> '版本号']
    ],

    'controller' => [
        'app\api\controller\Wx' => '用户',
        'app\api\controller\Subject' => '主题',
        'app\api\controller\Point' => '积分&奖励金',
        'app\api\controller\Task' => '任务',
        'app\api\controller\Other' => '其他',
    ],
];