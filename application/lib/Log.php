<?php
namespace app\lib;

use think\Log AS Tlog;
use think\Config;
use think\Request;
use think\Response;

class Log extends Tlog
{
    public static function init($config = []) {
        Config::load(APP_PATH.'api/config.php');
        empty($config) && $config = Config::get('log');
        parent::init($config);
    }

    public static function api(Request $req, Response $resp) {
        $type = $req->module() == 'sdk' ? 'sdk' : 'api';

        self::record('[ header ]'. json_encode($req->header() ), $type);
        self::record('[ req ]'. $req->getContent(), $type);
        self::record('[ resp ]'. $resp->getContent(), $type);
    }

    public static function message($message) {
        self::record( $message, 'api');
    }
}