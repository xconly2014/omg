<?php


namespace app\common\model;

/**
 * 微信小程序 分享到群
 * @package app\common\model
 */
class FormId extends  AbstractModel
{

    protected $connection = [
        'prefix' => 'wx_'
    ];

    protected $field = ['openid', 'formId'];
}