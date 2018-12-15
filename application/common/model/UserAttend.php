<?php
namespace app\common\model;

use traits\model\SoftDelete;

class UserAttend extends  AbstractModel
{
    use SoftDelete;

    protected $field = ['openid', 'attend_at'];
}