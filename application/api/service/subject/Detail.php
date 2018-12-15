<?php


namespace app\api\service\subject;


use app\api\service\AbstractApiHandler;

class Detail extends AbstractApiHandler
{
    public $rule = [
        'sid'  =>  'require|alphaDash|max:32',
        'result_id' => 'number',
        'openid'    => 'max:64'
    ];

    public function api() {
        $subject = S('subject')->getSubject($this->param('sid'));
        $subject->hit_num += 1;
        $subject->save();
        $data = S('subject')->detailInfo($subject);
        # 是否完成分享主题
        $data['has_share'] = 0;
        if ($this->param('openid')) {
            $user = M('user')->findByOpenid($this->param('openid'));
            if ($user) {
                $data['has_share'] = $this->hasShare($user) ? 1 : 0;
            }
        }

        # ad
        $ad = S('Ad')->findADBySubject($subject);
        $data['ad'] = $ad ? S('Ad')->simpleInfo($ad) : null;
        if ($this->param('result_id')) {
            $result = S('result')->getById($this->param('result_id'), $subject->id);
            $data['result'] = S('result')->detailInfo($result);
        }
        return $data;
    }

    /**
     * 是否完成分享主题
     * @param $user
     * @return bool
     */
    protected function hasShare($user) {
        $event = M('activityEvent')->where('hash', 'omg_share_sj')->find();
        if ($event) {
            return S('activity')->overRepeatTimes($user, $event);
        }
        return false;
    }
}