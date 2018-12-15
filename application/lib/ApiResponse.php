<?php
namespace app\lib;

use think\Request;
use think\response\Json;
use app\common\exception\ApiException;

class ApiResponse extends Json
{
    protected $_code = 1;

    protected $_message = 'success';

    protected $_data;

    protected $_sign;

    protected $_cache_expire;

    public function success(array $data) {
        $this->_data = $data;
        $this->tidy();
        return $this;
    }

    public function error(ApiException $e) {
        $this->_code = $e->getCode();
        $this->_message = $e->getMessage();
        $this->_data = [];
        $this->tidy();
        return $this;
    }

    protected function tidy() {
        isset($this->_data['_time']) || $this->_data['_time'] = time();
        $data = array(
            'code' => $this->_code,
            'message' => $this->_message,
            'data' => $this->_data,
        );

        $json = new Json($this->_data);
        $content = $json->getContent();
        $data['sign'] = S('apiAuth', 'api')->generateSign($content);
        $this->data($data);
    }

    public function cache($expire = 'S') {
        if( !is_numeric($expire) ) {
            switch (strtoupper($expire)) {
                case 'M':
                    $expire = 300;
                    break;
                case 'L':
                    $expire = 3600;
                    break;
                case 'S':
                default:
                    $expire = 60;
                    break;
            }
        }

        $this->_cache_expire = $expire;

        return $this;
    }

    public function getCacheExpire() {
        return $this->_cache_expire ? $this->_cache_expire : null;
    }
}
