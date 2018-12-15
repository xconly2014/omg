<?php


namespace app\common\service;


use app\common\exception\Factory;

class UserEvent
{
    public function getByRecordNo($record_no) {
        $obj = M('userEvent')->where('record_no', $record_no)->find();
        if (!$obj) {
            throw Factory::resourceNotFound('user event');
        }
        return $obj;
    }
}