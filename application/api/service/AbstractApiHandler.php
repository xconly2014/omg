<?php
namespace app\api\service;


use app\common\model\App;
use app\common\model\User;
use think\Config;
use think\Request;
use think\Validate;
use app\common\exception\Factory;

class AbstractApiHandler extends Validate
{
    /**
     * @var Request
     */
    protected $request;

    // 接口权限范围
    public $scope = ['api'];

    // 参数验证规则
    public $rule = [];



    public function __construct()
    {
        $this->request = Request::instance();

        isset($this->rule['sign']) || $this->rule('sign', 'require');
        isset($this->rule['timestamp']) || $this->rule('timestamp', 'require|integer|egt:' . ( time() - 300 ) . '|elt:' . ( time() + 300 ));
        isset($this->rule['version']) || $this->rule('version', 'require|integer');
        isset($this->rule['access_token']) || $this->rule('access_token', 'alphaDash|max:64');

        $this->message('timestamp.egt', 'Request timed out');
        $this->message('confirm.confirm', 'Confirm field not match!');

        parent::__construct();
    }


    /**
     * @param string $name
     * @param null $default
     * @param bool $is_ciphertext 是否为密文
     * @param string $filter
     * @return mixed
     * @throws
     */
    public function param($name = '', $default = null, $is_ciphertext = false, $filter = '') {
        $value = $this->request->post($name, $default, $filter);
        if($is_ciphertext) {
            $value = $this->encipherDecrypt($value);
            if(!$value) {
                throw Factory::failDecodeParam($name);
            }
        }
        return $value;
    }

    public function encipherDecrypt($value) {
        if($value) {
            $encipher = S('Encipher');
            if($app = $this->getApp()) {
                $encipher->setSecret($app->aes_secret);
            }
            $value = S('Encipher')->decrypt($value);
        }
        return $value;
    }

    public function encipherEncrypt($value) {
        if($value) {
            $encipher = S('Encipher');
            if($app = $this->getApp()) {
                $encipher->setSecret($app->aes_secret);
            }
            $value = S('Encipher')->encrypt($value);
        }

        return $value;
    }

    /**
     * 获取登录token
     * @return \app\common\redis\User
     */
    public function getUserToken() {
        return $this->request->user_token;
    }

    public function getAccessToken() {
        return $this->request->access_token;
    }

    public function listData($list, $list_key = 'list') {
        $result = array(
            $list_key => $list,
            'count' => count($list)
        );
        return $result;
    }

    /**
     * find app from token
     * @return null|App
     */
    public function getApp() {
        return $this->param('access_token') ? S('app')->getApp($this->getAccessToken()['client_id']) : null;
    }

    /**
     * find app form token or request parameter
     * @param $require
     * @return null|App
     * @throws \app\common\exception\CodeException
     */
    public function getAppWithParam($require = true) {
        $app = $this->getApp();
        if(!$app) {
            if($this->param('app_id') === '0') {
                $app = new \stdClass();
                $app->id = 0;
            } elseif( $this->param('app_id') ) {
                $app = S('app')->getApp($this->param('app_id'));
            }
        }
        if($require && !$app) {
            throw Factory::resourceNotFound('app');
        }
        return $app;
    }

    /**
     * @param $chk_lock
     * @return null
     * @throws \app\common\exception\LangException
     */
    public function getUser($chk_lock = true) {
        $user = $this->param('user_token') ? S('user')->getUser($this->getUserToken()['user_id']) : null;
        if($chk_lock && $user && $user->is_lock) {
            throw Factory::error('Account been closed', 12002);
        }
        return $user;
    }

    public function getAuthClient() {
        $client = $this->getAccessToken();
        if(!$client) {
            $api_config = Config::get('api');
            $data = [
                'client_secret' => $api_config['api_secret'],
                'scope' => 'api',
            ];
            $client = S('OAuth2')->getClientOrCreate('A0', $data);
        } else {
            if( !isset($client['client_secret']) || !$client['client_secret'] ) {
                $app = $this->getApp();
                $app && $client['client_secret'] = $app->client_secret;
            }
        }
        return $client;
    }

    public function getAuthScope() {
        $client = $this->getAuthClient();
        return $client['scope'];
    }

    protected function chkIp($ip = false) {
        if( !Config::get('api.debug') ) {
            $ip === false && $ip = $this->param('client_ip', $this->request->ip());
            if($ip) {
                $ipw = config('ip_whitelist');
                if(in_array($ip, $ipw)) {
                    return true;
                }
                $email = $this->param('email_account');

                $country = M('ipCountry')->getCountryByIp($ip);
                if($country == '中国') {
                    // throw Factory::error('Your area is not allowed to operate!');
                    if($email) {
                        if(stripos($email, '@hotmail.com') ) {
                            throw Factory::error('Invalid operation');
                        }
                        if(stripos($email, '@icloud.com') || stripos($email, '@live.cn') || stripos($email, '@outlook.com')) {
                            throw Factory::error('illegal operation');
                        }
                    }
                }

                $black_ip = ['117.28.112.189'];
                if(in_array($ip, $black_ip)) {
                    throw Factory::error('Have no right to access');
                }

                $arr = M('userLogin')
                    ->where('create_time', '>=', time() - 86400 * 3)
                    ->group('ip')
                    ->having('COUNT(DISTINCT user_id) > 10')
                    ->column('ip');

                if(in_array($ip, $arr)) {
                    throw Factory::error('Have no right to access!');
                }
            }
        }
    }
}