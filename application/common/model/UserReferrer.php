<?php
namespace app\common\model;

use traits\model\SoftDelete;

class UserReferrer extends  AbstractModel
{
    use SoftDelete;

    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $field = ['user_id', 'level', 'target_id', 'event_point', 'is_share_group_success'];

    /**
     * 从 $origin_time 为起始点开始分页
     * @param Query $query
     * @param $origin_time
     * @param $page
     * @param int $listRows
     */
    public function scopeOriginPage($query, $origin_time, $page, $listRows = 20) {
        $query->where('create_time', '<', $origin_time)->page($page, $listRows);
    }
}