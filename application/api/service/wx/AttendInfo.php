<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;
use think\Cache;


class AttendInfo extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64'
    ];

    protected $abe_icon = '/static/images/abe_icon.png';

    protected $base_award_point = 2;
    protected $bonus_point = 10;

    public function __construct()
    {
        $config = config('api.events');
        $this->base_award_point = $config['omg_attend_p2']['point'];
        $this->bonus_point = $config['omg_attend_p10']['point'];
        parent::__construct();
    }

    public function api() {
        $cacheKey = 'attendInfo:' . $this->param('openid');
        $data = Cache::get($cacheKey);
        if (!$data) {
            $today = strtotime(date('Y-m-d'));
            $openid = $this->param('openid');
            $user = S('user')->getUser($openid);
            $user_point = S('user')->refreshUserPoint($user);

            $data = [];
            $data['attend_times'] = $this->attendTimes($user);
            $data['attend_today'] = $this->attendToday($today);
            $data['is_attended'] = $this->isAttended($user, $today);
            $data['award'] = $this->getEventAward();
            $data['award_list'] = $this->awardList($data['attend_times']);
            $data['user_point'] = $user_point;
            $data['rate_to_amount'] = $this->rateToRMB($user_point);
            $data['rate_rmb_1000'] = $this->rateToRMB();
            $data['rate_icon'] = $this->getRateUpIcon();
            if ($data['is_attended'] === 1) {
                Cache::set($cacheKey, $data, 600);
            }
        }
        return $data;
    }

    /**
     * 我的签到次数
     * @param $user
     * @return mixed
     */
    protected function attendTimes($user) {
        return M('userEvent')->where('activity_id', 2)->where('user_id', $user->id)->count();
    }

    /**
     * 今日签到人数
     * @param $today
     * @return mixed
     */
    protected function attendToday($today) {
        return M('userEvent')->where('activity_id', 2)->where('create_time', '>=', $today)->count();
    }

    /**
     * 我今日是否签到
     * @param $user
     * @param $today
     * @return int
     */
    protected function isAttended($user, $today) {
        return M('userEvent')->where('activity_id', 2)->where('user_id', $user->id)->where('create_time', '>=', $today)->count() ? 1 : 0;
    }

    /**
     * 比例上涨图标
     * @return string
     */
    protected function getRateUpIcon() {
        $is_up = M('config')->getItem('rate-showUpIcon');
        return $is_up && $is_up == '1' ? completeUrl('/static/images/icon_up.png') : '';
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
     * 每日奖励数组
     * @param $attend_times
     * @return array
     */
    protected function awardList($attend_times) {
        $mod = $attend_times % 7;
        $mod === 0 && $attend_times !== 0 && $mod = 7;
        $list = [];
        for ($i = 1; $i <= 7; $i++) {
            $value = $i === 7 ? $this->bonus_point : $this->base_award_point;
            $is_attended = $i <= $mod ? 1 : 0;
            $list[] = [
                'value' => (string) $value,
                'is_attended' => $is_attended
            ];
        }
        return $list;
    }

    /**
     * 基本奖励信息
     * @return array
     */
    protected function getEventAward() {
        return [
            'icon' => completeUrl($this->abe_icon),
            'value' => (string) $this->base_award_point
        ];
    }
}