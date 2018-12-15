<?php
namespace app\api\service\task;

use app\api\service\AbstractApiHandler;

/**
 * 首次今日
 * @package app\api\service\wx
 */
class Enter extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'max:64'
    ];

    public function api() {
        if ($this->isShowShareGroup()) {
            $data = $this->dataForShareGroup();
        } else {
            $user = S('user')->getUser($this->param('openid'));
            $data = $this->dataForBase($user);
        }
        return $data;
    }

    /**
     * return array
     */
    protected function dataForShareGroup() {
        $data = [
            'model_key' => 'EM-1',
            'title' => '分享群后领取红包',
            'sub_title' => ''
        ];
        // $data['amount'] = M('activityEvent')->where('activity_id', 1)->cache()->sum('amount');
        $data['amount'] = 8800;
        $data['share'] = [
            'pic' => completeUrl('/static/images/share_rq.jpg'),
            'desc' => '我刚刚领取了88元红包，快来领取红包秒提现'
        ];
        return $data;
    }


    /**
     * @param $user
     * @return array
     */
    protected function dataForBase($user) {
        $referrer_success_num = S('user')->realReferrerNum($user);
        $title = '';
        $sub_title = '';

        $_tuple = ['start', 'next', 'amount'];
        $share_pic = completeUrl('/static/images/share_rq.jpg');
        $share_desc = '我刚刚领取了88元红包，快来领取红包秒提现';
        if ($referrer_success_num < 10) {
            $_tuple = [0, 10, 300];
            $share_pic = completeUrl('/static/images/share_rq_3.jpg');
            $share_desc = '我刚领取了3元红包，点击即可领取最高88元红包~';
        } elseif ($referrer_success_num < 30) {
            $_tuple = [10, 40, 900];
            $share_pic = completeUrl('/static/images/share_rq_9.jpg');
            $share_desc = '我刚领取了9元红包，点击即可领取最高88元红包~';
        } elseif ($referrer_success_num < 100) {
            $_tuple = [40, 140, 3000];
            $share_pic = completeUrl('/static/images/share_rq_30.jpg');
            $share_desc = '我刚领取了30元红包，点击即可领取最高88元红包~';
        } elseif ($referrer_success_num < 150) {
            $_tuple = [140, 290, 4500];
            $share_pic = completeUrl('/static/images/share_rq_30.jpg');
            $share_desc = '我刚领取了45元红包，点击即可领取最高88元红包~';
        }

        if ($_tuple[0] !== 'start') {
            $row_success_num = $referrer_success_num - $_tuple[0];
            $row_unit_amount = $_tuple[2] / ($_tuple[1] - $_tuple[0]) / 100;

            $title = '你还有红包未领取';
            $sub_title = $referrer_success_num == 0 ? '' : sprintf('已获得%.2f元 ', $row_success_num*$row_unit_amount);
            $sub_title .= sprintf('还需邀请%d人即可提现',  $_tuple[1] - $referrer_success_num);
        }

        $data = [
            'model_key' => 'EM-2',
            'title' => $title,
            'sub_title' => $sub_title
        ];
        $data['amount'] = $_tuple[2];
        $data['share'] = [
            'pic' => $share_pic,
            'desc' => $share_desc
        ];
        return $data;
    }

    /**
     * 是否显示分享群
     * @return bool
     */
    protected function isShowShareGroup() {
        $openid = $this->param('openid');
        if (!$openid) {
            return true;
        }
        $user = S('user')->getUser($this->param('openid'));
        $event = M('activityEvent')->where('hash', 'Invite-G-1')->find();
        $over = S('activity')->overRepeatTimes($user, $event);
        return $over ? false : true;
    }
}