<?php


namespace app\common\model;

use traits\model\SoftDelete;

class Ad extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $field = ['key', 'title', 'pic', 'type','detail'];

    protected static function init()
    {
        Ad::event('after_insert', function (Ad $entity) {
            if(!$entity->key) {
                $entity->key = $entity->idToAppId($entity->id);
            }
            $entity->save();
        });
        parent::init();
    }

    protected function idToAppId($id) {
        return 'AD-'.id2id($id, hexdec('AdModel'));
    }

    public function getPicAttr($value) {
        return completeUrl($value);
    }

    public function findByKey($key) {
        return $this->where('key', $key)->find();
    }
}