<?php


namespace app\common\service;


use app\common\exception\Factory;

class Ad
{
    public function getAd($key) {
        $ad = M('ad')->findByKey($key);
        if (!$ad) {
            throw Factory::resourceNotFound('ad');
        }
        return $ad;
    }

    public function findADBySubject(\app\common\model\Subject $subject) {
        $ads = $subject->ads;
        if(empty($ads)) {
            $ad = M('ad')->where('is_default', 1)->find();
        } else {
            $rand_keys = array_rand($ads);
            $ad_id = $ads[$rand_keys]->ad_id;
            $ad = M('ad')->findById($ad_id);
        }
        return $ad;
    }

    public function simpleInfo(\app\common\model\Ad $ad) {
        return [
            'ad_id' => $ad->getAttr('key'),
            'title' => $ad->title,
            'pic' => $ad->pic,
            'type' => $ad->getAttr('type'),
            'detail' => $ad->detail
        ];
    }
}