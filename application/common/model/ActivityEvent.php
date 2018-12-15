<?php


namespace app\common\model;

use traits\model\SoftDelete;

class ActivityEvent extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $field = ['activity_id','hash', 'name', 'point', 'amount','repeat_type', 'repeat_value', 'is_public', 'auto_withdraw'];

    protected static function init()
    {
        ActivityEvent::event('before_insert', function (ActivityEvent $event) {
            if(!$event->hash) {
                $event->hash = $event->generateHash();
            }
        });
        parent::init();
    }

    /**
     * 生成hash值
     * @return string
     */
    protected function generateHash() {
        return randomHash('Activity@'.$this->activity_id);
    }

    public function activity() {
        return $this->belongsTo('activity');
    }

    public function userEvent() {
        return $this->belongsTo('userEvent', 'event_hash', 'hash');
    }
}