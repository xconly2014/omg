<?php
namespace app\api\service\Task;

use app\api\service\AbstractApiHandler;

/**
 * 开启宝箱
 * @package app\api\service\Task
 */
class OpenTreasure extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|min:4|max:64',
    ];


    public function api() {
        $data = [];
        $user = S('user')->getUser($this->param('openid'));
        $res = $this->callEvent($user);
        $data['result'] = $res['result'] ? 1 : 0;
        if (isset($res['userEvent'])) {
            $data['record_no'] = $res['userEvent']->record_no;
            $data['event_hash'] = $res['userEvent']->event_hash;
            $data['point'] = $res['userEvent']->point;
            $data['collect_at'] = $res['userEvent']->collect_at;
        }
        return $data;
    }

    /**
     * 触发事件
     * @param $user
     * @return array
     */
    protected function callEvent($user) {
        $remaining_time = 0;
        $_param = [
            'user' => $user,
        ];

        $last_time = M('userEvent')->where('user_id', $user->id)
            ->where('activity_id', 9)->where('collect_at', '>', 0)->order('collect_at DESC')->value('collect_at');
        if ($last_time) {
            $remaining_time = 7200 - ( time() - $last_time );
        }
        if ($remaining_time <= 0) {
            $ot = new \app\api\behavior\events\OpenTreasure();
            $_param = $ot->run($_param);
            // \think\Hook::exec('app\\api\\behavior\\events\\OpenTreasure','run',$_param);
        }
        return $_param;
    }
}