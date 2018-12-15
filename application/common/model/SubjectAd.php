<?php


namespace app\common\model;


class SubjectAd extends  AbstractModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $field = ['subject_id', 'ad_id', 'type','detail'];

    public function subject() {
        return $this->belongsTo('subject');
    }
}