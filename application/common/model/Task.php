<?php


namespace app\common\model;

use traits\model\SoftDelete;

class Task extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $field = ['key', 'title', 'group', 'amount', 'point', 'repeat_type', 'repeat_value', 'publish_at'];

    protected static function init()
    {
        Task::event('before_insert', function (Task $entity) {
            if(!$entity->key) {
                $entity->key = $entity->generateKey();
            }
        });
        parent::init();
    }

    /**
     * 生成hash值
     * @return string
     */
    protected function generateKey() {
        return randomHash('Task'.$this->activity_id);
    }

    /**
     * 是否发布
     * @param Query $query
     * @param int $publish_flag
     */
    public function scopePublish($query, $publish_flag = 1) {
        if( !is_null($publish_flag) ) {
            if($publish_flag) {
                $query->where('publish_at', '>', 0);
            } else {
                $query->where('publish_at', 0);
            }
        }
    }
}