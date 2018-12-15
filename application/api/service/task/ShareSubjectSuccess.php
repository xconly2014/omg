<?php
namespace app\api\service\Task;

use app\api\service\AbstractApiHandler;

class ShareSubjectSuccess extends AbstractApiHandler
{
    public $rule = [
        'openid'    => 'require|min:4|max:64',
    ];


    public function api() {
        $user = S('user')->getUser($this->param('openid'));
        $this->callEvent($user);
        return [];
    }

    /**
     * 触发事件
     * @param $user
     */
    protected function callEvent($user) {
        $_param = [
            'user' => $user,
        ];
        \think\Hook::exec('app\\api\\behavior\\events\\ShareSubject','run',$_param);
    }
}