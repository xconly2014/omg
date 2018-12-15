<?php
namespace app\api\service\Task;

use app\api\service\AbstractApiHandler;
use think\Cache;

class ShareGroupSuccess extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|min:4|max:64',
        'openGId'    => 'require|max:64',
        'formId' => 'max:64'
    ];


    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        S('user')->saveFormID($this->param('openid'), $this->param('formId'));
        $shareGroup = $this->findOrCreateShareGroup();
        $userEvent = $this->onSuccess($user);
        return [];
    }

    /**
     * @return \app\common\model\ShareGroup
     */
    protected function findOrCreateShareGroup() {
        $share = M('shareGroup')
            ->where('openid', $this->param('openid'))
            ->where('openGId', $this->param('openGId'))
            ->find();
        $is_create = false;
        if (!$share) {
            $share = M('shareGroup')->getInstance();
            $share->openid = $this->param('openid');
            $share->openGId = $this->param('openGId');
            $share->save();
            $is_create = true;
        }
        $share->is_create = $is_create;
        return $share;
    }

    /**
     * 成功
     * @param $user
     * @return null|\app\common\model\UserEvent
     */
    protected function onSuccess($user) {
        $event = M('activityEvent')->where('hash', 'Invite-G-1')->find();
        $userEvent = S('activity')->call($user, $event);
        if ($userEvent) {
            // 标记一级邀请成功分享了群
            $referrer = M('userReferrer')->where('target_id', $user->id)->find();
            if ($referrer) {
                $referrer->is_share_group_success = 1;
                $referrer->save();
                $referrerUser = M('user')->findById($referrer->user_id);
                $referrer_success_num = S('user')->realReferrerNum($referrerUser);
                if ($referrer_success_num === 10) {
                    $event1 = M('activityEvent')->where('hash', 'Invite-F-1')->find();
                    S('activity')->call($user, $event1);
                } elseif ($referrer_success_num == 40) {
                    $event1 = M('activityEvent')->where('hash', 'Invite-F-2')->find();
                    S('activity')->call($user, $event1);
                } elseif ($referrer_success_num == 140) {
                    $event1 = M('activityEvent')->where('hash', 'Invite-F-3')->find();
                    S('activity')->call($user, $event1);
                } elseif ($referrer_success_num == 290) {
                    $event1 = M('activityEvent')->where('hash', 'Invite-F-4')->find();
                    S('activity')->call($user, $event1);
                }
            }
            Cache::rm('is_after_share_group:' . $user->id);
            Cache::set('is_after_share_group:' . $user->id, 1, 10);
        }
        return $userEvent;
    }
}