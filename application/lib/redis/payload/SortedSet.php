<?php


namespace app\lib\redis\payload;


class SortedSet extends AbstractRedisPayload
{
    // 类型
    const CLS = 'STS';
    /**
     * 放入一个元素且重置过期时间
     * @param $member
     * @param $score
     * @param null $key
     * @return int
     */
    public function add($member, $score = null, $key = null) {
        $key = $this->getFullKey($key);
        if(is_null($score)) {
            $membersAndScoresDictionary = $member;
        } else {
            $membersAndScoresDictionary = array($member => $score);
        }
        $result = $this->getClient()->zadd($key, $membersAndScoresDictionary);
        $result && $this->expireAt();
        return $result;
    }

    /**
     * batch addition
     * @param array $membersAndScoresDictionary
     * @param null $key
     * @return int
     */
    public function batchAdd(array $membersAndScoresDictionary, $key = null) {
        $key = $this->getFullKey($key);
        $result = 0;
        foreach (array_chunk($membersAndScoresDictionary, 250, true) as $chunk) {
            $result += $this->getClient()->zadd($key, $chunk);
        }
        $result && $this->expireAt();
        return $result;
    }

    /**
     * 获取成员积分
     * @param $member
     * @param null $key
     * @return string
     */
    public function get($member, $key=null) {
        $key = $this->getFullKey($key);
        return $this->getClient()->zscore($key, $member);
    }

    public function count($min = '-inf', $max = '+inf', $key = null) {
        $key = $this->getFullKey($key);
        if($min == 0 && $max == -1) {
            return $this->getClient()->zcard($key);
        }

        return $this->getClient()->zcount($key, $min, $max);
    }

    /**
     * 获取排名
     * @param $member
     * @param bool $rev true 从大到小
     * @param null $key
     * @return int
     */
    public function rank($member, $rev = true, $key=null) {
        $key = $this->getFullKey($key);
        if( $rev ) {
            return $this->getClient()->zrevrank($key, $member);
        } else {
            return $this->getClient()->zrank($key, $member);
        }
    }

    /**
     * 排行榜
     * @param $offset
     * @param $count
     * @param bool $rev
     * @param null $key
     * @param integer|string $min
     * @return array
     */
    public function rankList($offset, $count, $rev = true, $key=null, $min=1) {
        $options = [
            'withscores' => true,
            'limit' => [$offset, $count]
        ];
        return $this->rangeByScore($min, '+inf', $options, $rev, $key);
    }

    /**
     * 按索引获取集合
     * @param int $start
     * @param int $stop
     * @param array|null $options
     * @param bool $rev
     * @param null $key
     * @return array
     */
    public function range($start=0, $stop=-1, array $options = null, $rev = true, $key=null) {
        $key = $this->getFullKey($key);
        if( $rev ) {
            return $this->getClient()->zrevrange($key, $start, $stop, $options);
        } else {
            return $this->getClient()->zrange($key, $start, $stop, $options);
        }
    }

    /**
     * 按分数获取集合
     * @param int|string $min
     * @param int|string $max
     * @param array|null $options
     * @param bool $rev
     * @param null $key
     * @return array
     */
    public function rangeByScore($min='-inf', $max='+inf', array $options = [], $rev = true, $key=null) {
        $key = $this->getFullKey($key);
        if( $rev ) {
            return $this->getClient()->zrevrangebyscore($key, $max, $min, $options);
        } else {
            return $this->getClient()->zrangebyscore($key, $min, $max, $options);
        }
    }
}