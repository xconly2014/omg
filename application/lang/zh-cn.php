<?php
# 语言包： 中文

return [
    # E** 查看 app\common\exception\Factory
    # 请求响应
    'E12001' => '缺少参数 {:attribute}',
    'E12002' => '错误的参数：{:attribute}',
    'E12003' => '解码失败：{:attribute}',

    # 逻辑相关， 控制器和模型
    'E13001' => '访问资源不存在 {:name}',
    'E13002' => '{:name}已存在',
    'E13003' => '{:name}已被使用',

    # 参考 thinkphp\lang\zh-cn.php，且不需重复定义
    'System error'      => '系统错误',
    'Request timed out' => '请求超时',
    'Please logon again' => ' 请重新登录',

    # ---------------------------------- （需配置英文版）
    'GAME-DETAIL-INTRO' => '简介', // 游戏详情 - 简介
    'GAME-DETAIL-UPDATE' => '更新内容', // 游戏详情 - 更新内容
    'GAME-DETAIL-PACKAGE' => '详细信息', // 游戏详情 - 安装包信息

    'GAME-FIND-NEW' => '新游发现', // 发现 - 开放类游戏
    'GAME-FIND-APPOINT' => '新游预约', // 发现 - 预约类游戏
    'GAME-FIND-TEST' => '游戏测试', // 发现 - 测试类游戏

    # 游戏排行等标签
    'TAG-HOT' => '热门',
    'TAG-NEW' => '新游',
    'TAG-APPOINT' => '预约',
    'TAG-HOT-PLAY' => '热玩',
    'TAG-DEV' => '厂商',
    'TAG-POINT' => 'ABE Token',
    'TAG-USER-POINT' => 'User',
    'TAG-APP' => '应用',

    # 积分交易类型
    'PO-PAY' => '支付',
    'PO-TOP-UP' => '充值',
    'PO-DRAW' => '提现',
    'PO-EVENT' => '完成任务',
    'PO-MINE' => '贡献力奖励',
    'PO-BUY-ITEM' => '购买商品',
    'PO-MCH-PAY' => '支付商户订单',

    # 邮件
        // -- 账号激活
    'MAIL-AA-SUBJECT' => '[ABE] 账号激活',
    'MAIL-AA-CONTENT' => "点击以下链接激活账号：",
        // -- 邀请注册
    'MAIL-RU-SUBJECT' => '[ABE] 您的好友{:name}邀请您加入ABE',
    'MAIL-RU-CONTENT' => "您的好友{:name}邀请您加入ABE：",

    # ---------------------------------- （无需配置英文语言，但需配置其他语言版）
    'Please log in first' => '请先登录',
];