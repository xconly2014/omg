<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use think\Config;

class My extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64'
    ];

    public function api() {
        $data = [];
        $user = S('user')->getUser($this->param('openid'));
        $data['point'] = S('user')->getUserPoint($user);
        $data['note'] = '*联系客服了解积分兑换红包相关信息*';
        $data['note_modal'] = '换算比例';
        $data['note_modal_body'] = '每天的广告收入会按照一定比例返回给用户，所以如果广告收入越多积分能兑换的红包金额越高!';
        $data['rate_rmb_1000'] = $this->rateToRMB();
        $data['user'] = S('user')->simpleInfo($user);
        $data['referrerStat'] = $this->statReferrer($user);
        $data['task'] = $this->taskList($user);
        return $data;
    }

    /**
     * 积分转？元
     * @param int $point
     * @return string
     */
    protected function rateToRMB($point = 1000) {
        $rate = M('config')->getItem('rate-pointToRMB');
        $amount = (int) bcdiv($point, $rate);
        return number_format($amount / 100, 2, '.', '');
    }

    /**
     *
     * @param $user
     * @return array
     */
    protected function taskList ($user) {
        $model = M('Task');
        $model->publish();
        $model->where('group', null);
        $list = [];
        $rows = $model->select();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $item = [
                    'key' => $row->key,
                    'title' => $row->title,
                    'detail' => ['point'=>$row->point],
                    'is_finished' => 0
                ];
                if ($item['key'] == 'T-001') { // 每日签到
                    $item['is_finished'] = $this->isAttended($user);
                }
                if ($item['key'] == 'T-002') { // 绑定手机
                    $item['is_finished'] = $user->is_tel_bind;
                }
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * 邀请统计
     * @param $user
     * @return array
     */
    protected function statReferrer($user) {
        $config = Config::get('api.events');
        $referrer_v1 = M('userReferrer')->where('user_id', $user->id)->where('level', 1)->cache()->count();
        $referrer_v2 = M('userReferrer')->where('user_id', $user->id)->where('level', 2)->cache()->count();
        return [
            'v1' => [
                'times' => $referrer_v1,
                'point' => $config['omg_referrer_v1']['point']
            ],
            'v2' => [
                'times' => $referrer_v2,
                'point' => $config['omg_referrer_v2']['point']
            ],
        ];
    }

    /**
     * 是否已签到
     * @param $user
     * @return int
     */
    protected function isAttended($user) {
        $today = strtotime(date('Y-m-d'));
        return M('UserAttend')->where('openid', $user->openid)->where('attend_at', '>=', $today)->count() ? 1 : 0;
    }
}