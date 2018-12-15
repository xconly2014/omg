<?php
namespace app\api\service\wx;

use app\api\service\AbstractApiHandler;

/**
 * 我邀请的用户
 * @package app\api\service\wx
 */
class Referrer extends AbstractApiHandler
{
    public $rule = [
        'page'  =>  'integer|>=:1',
        'page_rows' => 'integer|>=:1',
        'page_time' => 'integer',
        'openid'    => 'require|max:64',
        'level' => 'number|in:1,2'
    ];

    public function api() {
        $openid = $this->param('openid');
        $level = $this->param('level', 1);
        $user = S('user')->getUser($openid);
        $list = $this->selectRows($user, $level);
        return $this->listData($list);
    }

    protected function selectRows($user, $level) {
        $model = M('userReferrer');
        $model->originPage(
            $this->param('page_time', time() ),
            $this->param('page', 1),
            $this->param('page_rows', 50)
        );

        $model->where('user_id', $user->id)
            ->where('level', $level);
        $model->order('id DESC');
        $rows = $model->select();
        $list = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $target = M('user')->findById($row->target_id);
                if ($target) {
                    $list[] = S('user')->simpleReferrerRow($target, $row);
                }
            }
        }
        return $list;
    }
}