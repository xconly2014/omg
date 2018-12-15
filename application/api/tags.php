<?php
return [
    // 模块初始化
    'action_begin' => [
        "app\\api\\behavior\\IpTimesLimit",
        "app\\api\\behavior\\RequestValidate",
        "app\\api\\behavior\\VerifySign",
        "app\\api\\behavior\\RequestCache", // 读取缓存放在验证参数等之后
    ],
    'response_send' => [
        "app\\api\\behavior\\RequestCache",
    ],
];