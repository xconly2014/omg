<?php


namespace app\lib\redis\payload;


class Set extends AbstractRedisPayload
{
    // 类型
    const CLS = 'SET';

    public function exists($key = null, $field = null) {
        $key = $this->getFullKey($key);
        if(is_null($field)) {
            return $this->getClient()->exists($key);
        } else {
            return $this->getClient()->sismember($key, $field);
        }
    }

    /**
     * 放入一个元素且重置过期时间
     * @param $members
     * @param null $key
     * @return int
     */
    public function add($members, $key = null) {
        $key = $this->getFullKey($key);
        is_string($members) && $members = array($members);
        $result = $this->getClient()->sadd($key, $members);
        $result && $this->expireAt();
        return $result;
    }

    public function count($key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->scard($key);
    }

    /**
     * 随机取出若干成员
     * @param int $count
     * @param string $key
     * @return string
     */
    public function pop($count = null, $key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->spop($key, $count);
    }

    /**
     * 随机获取若干成员
     * @param null $count
     * @param null $key
     * @return string
     */
    public function rand($count = null, $key = null) {
        return $this->getClient()->srandmember($key, $count);
    }

    /**
     * 移除成员
     * @param $member
     * @param null $key
     * @return int
     */
    public function rem($member, $key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->srem($key, $member);
    }

    public function all($key = null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->smembers($key);
    }
}