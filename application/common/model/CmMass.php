<?php


namespace app\common\model;

use traits\model\SoftDelete;

/**
 * 微信小程序 客服消息群发记录
 * @package app\common\model
 */
class CmMass extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $connection = [
        'prefix' => 'wx_'
    ];

    protected $field = ['subject', 'detail', 'status', 'msgtype'];

    public function setDetailAttr(array $val) {
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }

    public function getDetailAttr($val) {
        return json_decode($val, true);
    }
}