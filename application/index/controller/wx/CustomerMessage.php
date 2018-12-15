<?php


namespace app\index\controller\wx;


use think\Config;
use think\Controller;

class CustomerMessage extends Controller
{
    /**
     * 第二步：验证消息的确来自微信服务器
     */
    public function verify() {
        if ($this->checkSignature()) {
            return $this->request->query('echostr');
        } else {
            return 'fail';
        }
    }

    private function checkSignature()
    {
        $request = $this->request;
        $config  = Config::get('api.wx_customer_message');
        $signature = $request->query('signature');
        $timestamp = $request->query('timestamp');
        $nonce = $request->query('nonce');

        $token = $config['Token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if ($tmpStr == $signature ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 接收消息
     */
    public function receive() {
        $data = $this->request->post();
        $message = M('CustomerMessage')->getInstance($data);
        $message->save();
        /*switch ($this->request->post('MsgType')) {
            case 'text':
                break;
            case 'image':
                break;
            case 'miniprogrampage':
                break;
            case 'event':
                break;
        }*/
    }
}