<?php
# 语言包： 英文

return [
    # E** 查看 app\common\exception\Factory
    # 请求响应
    'E12001' => 'missing param {:attribute}',
    'E12002' => 'invalid {:attribute}',
    'E12003' => 'Decoding failure：{:attribute}',

    # 逻辑相关， 控制器和模型
    'E13001' => 'Access to resources does not exist: {:name}',
    'E13002' => '{:name} already exist',
    'E13003' => '{:name} has been used',

    # 参考 thinkphp\lang\zh-cn.php，且不需重复定义
    'System error'      => 'System error',
    'Request timed out' => 'Request timed out',
    'Please logon again' => 'Please logon again',

    # ---------------------------------- （需配置英文版）
    'GAME-DETAIL-INTRO' => 'Introduction', // 游戏详情 - 简介
    'GAME-DETAIL-UPDATE' => 'Updates', // 游戏详情 - 更新内容
    'GAME-DETAIL-PACKAGE' => 'More Information', // 游戏详情 - 安装包信息

    'GAME-FIND-NEW' => 'New', // 发现 - 开放类游戏
    'GAME-FIND-APPOINT' => 'Appoint', // 发现 - 预约类游戏
    'GAME-FIND-TEST' => 'Test', // 发现 - 测试类游戏

    # 游戏排行等标签
    'TAG-HOT' => 'Hot',
    'TAG-NEW' => 'New',
    'TAG-APPOINT' => 'Appoint',
    'TAG-HOT-PLAY' => 'Hot Play',
    'TAG-DEV' => 'Developer',
    'TAG-POINT' => 'ABE Token',
    'TAG-USER-POINT' => 'User',
    'TAG-APP' => 'App',

    # 积分交易类型
    'PO-PAY' => 'Pay',
    'PO-TOP-UP' => 'Top UP',
    'PO-DRAW' => 'Withdraw',
    'PO-EVENT' => 'Activity reward',
    'PO-MINE' => 'Credit reward',
    'PO-BUY-ITEM' => 'Buy',
    'PO-MCH-PAY' => 'MCH Pay',
];