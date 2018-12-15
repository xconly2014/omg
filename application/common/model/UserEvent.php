<?php


namespace app\common\model;

use traits\model\SoftDelete;

class UserEvent extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $field = ['record_no', 'user_id','activity_id','event_hash', 'point', 'amount', 'attach','collect_at', 'withdraw_id'];

    protected static function init()
    {
        UserEvent::event('after_insert', function (UserEvent $entity) {
            if(!$entity->record_no) {
                $entity->record_no = $entity->makeTradeNo($entity->id);
            }
            $entity->save();
        });
        parent::init();
    }

    protected function makeTradeNo($id) {
        $id = str_pad($id % 10000, 4, "0", STR_PAD_LEFT); // 4
        $number = date('YmdH') . $id . date('is');  // 18 个字符
        return 'UA'.$number; // 20
    }

    public function activityEvent() {
        $query = $this->hasOne('activityEvent', 'hash', 'event_hash');
        return $query;
    }

    public function findByUserIDAndHash($user_id, $event_hash) {
        return M('userEvent')
            ->where('user_id',$user_id)
            ->where('event_hash', $event_hash)
            ->find();
    }
}