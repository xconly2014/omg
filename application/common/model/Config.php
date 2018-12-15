<?php
namespace app\common\model;

use traits\model\SoftDelete;

class Config extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $pk = 'name';

    protected $field = ['name','value'];

    public function getItem($name) {
        return $this->where('name', $name)->value('value');
    }

    public function setItem($name, $value) {
        return $this->save(['name' => $name, 'value' => $value]);
    }

    public function removeItem($name) {
        return self::destroy($name);
    }

    public function allItem() {
        $rows = $this->cache()->select();
        $list = [];
        if (!empty($rows)) {
            foreach ($rows as $config) {
                $list[ $config['name'] ] = $config;
            }
        }
        return $list;
    }
}