<?php


namespace app\api\behavior;


use app\common\exception\Factory;
use think\Request;

class IpTimesLimit
{
    public function run(&$action) {
        $request = Request::instance();
        #if($request->module() == 'api') {}
        $ip = $request->param('client_ip') ? $request->param('client_ip') : $request->ip();
        $ipw = Config('ip_whitelist');
        if($ipw && !in_array($ip, $ipw)) {
            $times = R('IpLimit')->init($ip)->incrInRequest();
            if($times > 60) {
                R('IpLimit')->delayExpire(10, 900);
                throw Factory::error('Request too fast!');
            }
        }
    }
}