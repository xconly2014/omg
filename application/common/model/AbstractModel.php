<?php
namespace app\common\model;


use think\Model;

abstract class AbstractModel extends Model
{
    // 字段验证规则
    protected $validate;
    // 数据表主键 复合主键使用数组定义 不设置则自动获取
    protected $pk = 'id';
    // 数据表字段信息 留空则自动获取
    protected $field = [];
    // 隐藏属性
    protected $hidden = ['delete_time'];

    protected $deleteTime = 'delete_time';

    public function __construct($data = [])
    {
        array_push($this->field, 'delete_time');
        parent::__construct($data);
    }

    public function getInstance($data = []) {
        $class = get_class($this);
        return new $class($data);
    }

    /**
     * 读取时，抑制不存在的数据错误直接返回空字符
     * @param string $name
     * @return mixed|string
     */
    public function __get($name)
    {
        try {
            $value = parent::__get($name);
        } catch (\Exception $e) {
            $value = '';
        }
        null === $value && $value = '';
        return $value;
    }

    public function getAttr($name)
    {
        $value = parent::getAttr($name);
        if(  $value === Null ) {
            $value = '';
        } elseif ($value === false) {
            $value = 0;
        } elseif ($value === true) {
            $value = 1;
        }
        return $value;
    }

    public function getCreateTimeAttr($value)
    {
        return (int) $value;
    }

    public function getUpdateTimeAttr($value)
    {
        return (int) $value;
    }

    public function findById($id) {
        return $this->where('id', $id)->find();
    }

    public function findByWhere(array $where) {
        return $this->where($where)->find();
    }
}