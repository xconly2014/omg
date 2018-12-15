<?php


namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use app\common\exception\Factory;
use app\lib\wxEncrypt\WXBizDataCrypt;
use think\Config;

class Decode extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64',
        'encryptedData'    => 'require|min:1',
        'iv'    => 'require|min:1',
    ];

    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        if (!$user->session_key) {
            throw Factory::error('Please log in first', 13001);
        }

        $config = Config::get('api.wx');
        $pc = new WXBizDataCrypt($config['AppID'], $user->session_key);
        $errCode = $pc->decryptData($this->param('encryptedData'), $this->param('iv'), $data );

        if ($errCode != 0) {
            throw Factory::error('WXErr:' . $errCode);
        }
        return [
            'result' => json_decode($data)
        ];
    }
}