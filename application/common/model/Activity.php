<?php


namespace app\common\model;

use traits\model\SoftDelete;

class Activity extends  AbstractModel
{
    use SoftDelete;
    protected $field = ['key', 'subject', 'intro','start_time', 'end_time', 'publish_at','total_point', 'total_amount', 'payload', 'show_type'];

    protected static function init()
    {
        Activity::event('after_insert', function (Activity $entity) {
            if(!$entity->key) {
                $entity->key = 'T-' . str_pad($entity->id % 10000, 4, "0", STR_PAD_LEFT);;
            }
        });
        parent::init();
    }

    public function events() {
        return $this->hasMany('ActivityEvent', 'activity_id');
    }
}