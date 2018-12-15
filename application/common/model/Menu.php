<?php


namespace app\common\model;

class Menu extends  AbstractModel
{
    protected $field = ['parent_id', 'name', 'path','class_icon', 'priority'];


    public function getPathAttr($value) {
        strpos($value, '/') === 0 || $value = '/'.$value;
        return completeUrl($value, true);
    }
}