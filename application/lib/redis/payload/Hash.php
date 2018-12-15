<?php


namespace app\lib\redis\payload;

use InvalidArgumentException;
use think\Exception;

class Hash extends AbstractRedisPayload
{
    // 类型
    const CLS = 'H';

    // 数据表字段信息 留空则自动获取
    protected $field = [];

    // 数据信息
    protected $data = [];

    // 过期时间
    protected $expire =3600;


    /**
     * 修改器 设置数据对象值
     * @access public
     * @param string $name  属性名
     * @param mixed  $value 属性值
     * @param array  $data  数据
     * @return $this
     */
    public function setAttr($name, $value, $data = []) {
        $method = 'set' . ucfirst($name) . 'Attr';
        if (method_exists($this, $method)) {
            $value = $this->$method($value, array_merge($this->data, $data));
        }
        // 设置数据对象属性
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function getAttr($name) {
        $value    = $this->getData($name);
        // 检测属性获取器
        $method = 'get' . ucfirst($name) . 'Attr';
        if (method_exists($this, $method)) {
            $value = $this->$method($value, $this->data);
        }
        return $value;
    }

    /**
     * 获取对象原始数据 如果不存在指定字段返回false
     * @access public
     * @param string $name 字段名 留空获取全部
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getData($name = null)
    {
        if (is_null($name)) {
            return $this->data;
        } elseif (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new InvalidArgumentException('property not exists:' . get_called_class() . '->' . $name);
        }
    }


    /**
     * 设置数据对象值
     * @access public
     * @param mixed $data  数据或者属性名
     * @param mixed $value 值
     * @return $this
     */
    public function data($data, $value = null)
    {
        if (is_string($data)) {
            $this->data[$data] = $value;
        } else {
            // 清空数据
            $this->data = [];
            if (is_object($data)) {
                $data = get_object_vars($data);
            }
            if (true === $value) {
                // 数据对象赋值
                foreach ($data as $key => $value) {
                    $this->setAttr($key, $value, $data);
                }
            } else {
                $this->data = $data;
            }
        }
        return $this;
    }

    /**
     * 修改器 设置数据对象的值
     * @access public
     * @param string $name  名称
     * @param mixed  $value 值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setAttr($name, $value);
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttr($name);
    }

    /**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name)
    {
        try {
            if (array_key_exists($name, $this->data)) {
                return true;
            } else {
                $this->getAttr($name);
                return true;
            }
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    protected function filterData() {
        $data = [];
        $fileds = $this->getFields();
        if(!empty($fileds)) {
            foreach ($this->data as $field => $val) {
                if(in_array($field, $fileds)) {
                    $data[$field] = $val;
                }
            }
        }
        return $data;
    }

    /**
     * 初始设置该模型数据
     * @return array
     */
    protected function getFields() {
        if(empty($this->field)) {
            $fields = $this->getClient()->hkeys($this->key);
            empty($fields) || $this->field = $fields;
        } else {
            $fields = $this->field;
        }
        return array_unique($fields);
    }

    protected function chkData() {
        if( empty($this->data) ) {
            throw new Exception('Nothing For Save:'.  get_called_class() . '->save()');
        }
        return $this;
    }

    public function exists($key = null, $field = null) {
        $key = $this->getFullKey($key);
        if(is_null($field)) {
            return $this->getClient()->exists($key);
        } else {
            return $this->getClient()->hexists($key, $field);
        }
    }

    public function del($keys = null) {
        $keys || $keys = array( $this->key );
        is_string($keys) && $keys = array($keys);
        $full = [];
        foreach ($keys as $key) {
            $full[] = $this->getFullKey($key);
        }
        return $this->getClient()->del($full);
    }

    /**
     * 从哈希表获取一条记录
     * @param $key
     * @return static|false
     */
    public function find($key = null) {
        $key = $this->getFullKey($key);
        if(!$this->getClient()->exists($key)) {
            return false;
        }
        $fields =  $this->getFields();

        $values = $this->getClient()->hmget($key, $fields);

        $obj = $this->newInstance();
        foreach ($fields as $index => $key) {
            $obj->data[ $key ] = $values[ $index ];
        }
        return $obj;
    }


    /**
     * @param null $key
     * @param array $data
     * @param int $expire
     * @return mixed
     */
    public function save($key = null, $expire = null, $data = []) {
        $key && $this->setKey($key);
        is_numeric($expire) && $this->setExpire($expire);

        empty($data) || $this->data($data);
        $this->chkData();

        $data = $this->filterData();

        // 保存
        $result = $this->getClient()->hmset($this->getFullKey(), $data);
        $result && $this->expireAt();
        return $data;
    }
}