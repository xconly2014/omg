<?php


namespace app\lib\redis\payload;


class String extends AbstractRedisPayload
{
    // 类型
    const CLS = 'STR';

    // 数据信息
    /**
     * @var string
     */
    protected $data;

    public function get($key = null) {
        $key = $this->getFullKey($key);
        $this->data = $this->getClient()->get($key);
        return $this->data;
    }

    public function getData() {
        return $this->data;
    }

    public function set($value, $key = null) {
        $key = $this->getFullKey($key);
        $result  = $this->getClient()->set($key, $value);
        if($result) {
            return $this->expireAt();
        }
        return $result;
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

    public function incr($key = null) {
        $key = $this->getFullKey($key);
        $value = $this->getClient()->incr($key);
        if($value <= 1) {
            $this->getClient()->expire($key, $this->expire);
        }
        return $value;
    }
}