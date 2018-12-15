<?php


namespace app\common\model;

use traits\model\SoftDelete;

class AdEvent extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $field = ['ad_id', 'action', 'openid'];
}