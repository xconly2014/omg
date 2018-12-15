<?php
namespace app\api\service\other;

use app\api\service\AbstractApiHandler;

class Ad extends AbstractApiHandler
{
    public $rule = [
        'ad_id'    => 'require|max:64',
    ];


    public function api() {
        $key = $this->param('ad_id');
        $ad = S('ad')->getAd($key);
        return S('ad')->simpleInfo($ad);
    }
}