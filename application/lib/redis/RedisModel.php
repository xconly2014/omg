<?php


namespace app\lib\redis;


use app\lib\redis\payload\Hash;
use app\lib\redis\payload\Set;
use app\lib\redis\payload\SortedSet;
use think\Exception;
use Predis\Client;
use think\Config;

abstract class RedisModel
{
    /**
     * @var Client;
     */
    protected static $client;

    protected static $payload = [];

    protected $options = [
        'string' => [
            'default' => [
                'key_prefix' => 'Default',
                'expire' => 3600,
            ],
        ],
        'hash' => [
            'default' => [
                'key_prefix' => 'Default',
                'field' => [],
                'expire' => 3600,
            ]
        ],
        'set' => [
            'default' => [
                'key_prefix' => 'Default',
                'expire' => 3600,
            ]
        ],
        'sortedSet' => [
            'default' => [
                'key_prefix' => 'Default',
                'expire' => 3600,
            ]
        ],
    ];

    protected $id;

    abstract public function init($id);

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return RedisModel
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Client
     */
    protected function client() {
        if(!self::$client) {
            $config = Config::get('predis_client');
            self::$client = new Client($config['parameters'], $config['options']);
            self::$client->select($config['db_default']);
        }
        return self::$client;
    }
    /**
     * @param $name
     * @param $key
     * @return Hash
     */
    public function hash($name, $key) {
        $payload = $this->getPayload('hash', $name);
        if($payload && !$payload->getKey()) {
            $payload->setKey($key);
        }
        return $payload;
    }

    /**
     * @param $name
     * @param $key
     * @return \app\lib\redis\payload\String
     */
    public function string($name, $key) {
        $payload = $this->getPayload('string', $name);
        if($payload) {
            $payload->setKey($key);
        }
        return $payload;
    }

    /**
     * @param $name
     * @param $key
     * @return SortedSet
     */
    public function sortedSet($name, $key = '') {
        $payload = $this->getPayload('sortedSet', $name);
        if($payload && !$payload->getKey()) {
            $payload->setKey($key);
        }
        return $payload;
    }

    /**
     * @param $name
     * @param $key
     * @return Set
     */
    public function set($name, $key = '') {
        $payload = $this->getPayload('set', $name);
        if($payload && !$payload->getKey()) {
            $payload->setKey($key);
        }
        return $payload;
    }

    /**
     * @param $type
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getPayload($type, $name) {
        $class = get_class($this);
        $name = lcfirst($name);
        $payload= "\\app\\lib\\redis\\payload\\".ucfirst($type);
        isset(self::$payload[$class])|| self::$payload[$class] = [];
        isset(self::$payload[$class][$type])|| self::$payload[$class][$type] = [];

        if (isset( $this->options[$type][$name] )) {
            if( !isset( self::$payload[$class][$type][$name] ) ) {
                self::$payload[$class][$type][$name] = new $payload($this->client(),  $this->options[$type][$name]);
            }
            return  self::$payload[$class][$type][$name];
        } else {
            throw new Exception('Options Not Found: '.$type.'.'.$name);
        }
    }
}