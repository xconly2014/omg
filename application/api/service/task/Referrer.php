<?php
namespace app\api\service\task;

use app\api\service\AbstractApiHandler;
use think\Cache;

/**
 * 签到
 * @package app\api\service\wx
 */
class Referrer extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|max:64'
    ];

    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        $data = [];
        $data['stat'] = $this->statInfo($user);
        $data['stat']['note'] = '邀请好友最高可获得88元现金和海量ABE积分，成功邀请1位有效用户可获得0.3元（被邀请用户必须完成“分享到群”的任务，才算有效用户），二级邀请可获得2ABE积分。';
        $data['task'] = $this->groupInfo($user);
        return $data;
    }

    /**
     * 邀请任务信息
     * @param $user
     * @return array
     */
    protected function groupInfo($user) {
        $activity = M('activity')->findById(1);
        if ($activity) {
            return S('activity')->groupInfo($activity, $user);
        }
        return [];
    }

    /**
     * 统计信息
     * @param $user
     * @return array|mixed
     */
    protected function statInfo($user) {
        $cacheKey = 'Task-Referrer::stat:'.$user->id;
        $data = Cache::get($cacheKey);
        if (!$data) {
            $data = [];
            $data['referrer_lv1'] = 0;
            $data['real_referrer_lv1'] = 0;
            $data['referrer_lv2'] = 0;
            $data['real_referrer_lv2'] = 0;

            // 邀请信息
            $refRows = M('userReferrer')->where('user_id', $user->id)->select();
            if (!empty($refRows)) {
                foreach ($refRows as $row) {
                    if ($row->level == 1) {
                        $data['referrer_lv1']++;
                        if ($row->is_share_group_success) {
                            $data['real_referrer_lv1']++;
                        }
                    }

                    if ($row->level == 2) {
                        $data['referrer_lv2']++;
                        if ($row->is_share_group_success) {
                            $data['real_referrer_lv2']++;
                        }
                    }
                }
            }
            // 已收益金额
            $data['income_amount'] = M('userEvent')
                ->where('user_id', $user->id)
                ->where('activity_id', 1)
                ->where('event_hash', '<>', 'Invite-G-1')
                ->sum('amount');
            // 已收益积分
            $data['income_point'] = M('userEvent')
                ->where('user_id', $user->id)
                ->where('activity_id', 4)
                ->sum('point');
            Cache::set($cacheKey, $data, 300);
        }
        return $data;
    }
}