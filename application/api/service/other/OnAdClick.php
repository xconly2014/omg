<?php
namespace app\api\service\other;

use app\api\service\AbstractApiHandler;

class OnAdClick extends AbstractApiHandler
{
    public $rule = [
        'ad_id'    => 'require|max:64',
        'openid'    => 'max:64'
    ];

    public function api() {
        $key = $this->param('ad_id');
        $ad = S('ad')->getAd($key);
        $adEvent = M('adEvent');
        $adEvent->ad_id = $ad->id;
        $adEvent->action = 'CLICK';
        $adEvent->openid = $this->param('openid');
        $adEvent->save();
        return [];
    }
}