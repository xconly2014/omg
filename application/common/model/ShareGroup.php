<?php


namespace app\common\model;

use traits\model\SoftDelete;

/**
 * 微信小程序 分享到群
 * @package app\common\model
 */
class ShareGroup extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $connection = [
        'prefix' => 'wx_'
    ];

    protected $field = ['openid', 'openGId'];
}