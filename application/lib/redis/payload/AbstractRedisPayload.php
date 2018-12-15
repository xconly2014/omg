<?php


namespace app\lib\redis\payload;

use Predis\Client;
use think\Exception;

class AbstractRedisPayload
{
    // 类型
    const CLS = 'STR';
    // 数据key值
    protected $key;
    // key 前缀
    protected $key_prefix;

    protected $expire;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client, array $options)
    {
        $this->client = $client;

        $properties = array_keys( get_object_vars($this) );
        foreach ( $options as $key => $value) {
            if(in_array($key, $properties)) {
                $this->$key = $value;
            }
        }
    }

    public function getClient() {
        return $this->client;
    }

    public function newInstance() {
        return new static($this->client, get_object_vars($this));
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    public function getKey() {
        return $this->key;
    }

    protected function getFullKey($key = null) {
        if(is_null($key)) {
            $this->chkKey();
            $key = $this->key;
        }
        return $this::CLS.':'.$this->key_prefix . ':' . $key;
    }

    protected function chkKey() {
        if(is_null($this->key)) {
            throw new Exception('Key Undefined:' . get_called_class());
        }
        return $this;
    }

    protected function getExprieAt($timestamp = null) {
        if( !$this->expire ) return 0;

        is_null($timestamp) && $timestamp = time() + $this->expire;
        return $timestamp;
    }

    public function setExpire($value) {
        $this->expire = $value;
        return $this;
    }

    /**
     * @param int $timestamp 时间戳
     * @param null|string $key
     * @return int
     */
    public function expireAt($timestamp = null, $key = null) {
        $key = $this->getFullKey($key);
        $expire_at = $this->getExprieAt($timestamp);
        if($expire_at) {
            $this->getClient()->expireat($key, $expire_at);
        }
        return $expire_at;
    }

    public function expire($seconds, $key = null) {
        $key = $this->getFullKey($key);
        $this->getClient()->expire($key, $seconds);
    }

    public function exists($key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->exists($key);
    }

    /**
     * 剩余存活秒数
     * @param null|string $key
     * @return int
     */
    public function ttl($key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->ttl($key);
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
}