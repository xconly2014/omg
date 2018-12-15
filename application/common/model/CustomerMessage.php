<?php


namespace app\common\model;

use traits\model\SoftDelete;

class CustomerMessage extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $connection = [
        'prefix' => 'wx_'
    ];

    protected $field = ['action', 'massId', 'ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Content', 'MsgId',
        'PicUrl', 'MediaId', 'Title', 'AppId', 'PagePath', 'ThumbUrl', 'ThumbMediaId', 'Event', 'SessionFrom'];
}