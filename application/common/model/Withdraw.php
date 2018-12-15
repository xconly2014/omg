<?php
namespace app\common\model;

use traits\model\SoftDelete;

class Withdraw extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['trade_type', 'trade_no', 'openid', 'point', 'amount', 'is_point_change',
        'transfer_status', 'transfer_at', 'transfer_no', 'transfer_err'];

    protected static function init()
    {
        Withdraw::event('after_insert', function (Withdraw $entity) {
            if(!$entity->trade_no) {
                $entity->trade_no = $entity->makeTradeNo($entity->id);
            }
            $entity->save();
        });
        parent::init();
    }

    protected function makeTradeNo($id) {
        $id = str_pad($id % 10000, 4, "0", STR_PAD_LEFT); // 4
        $number = date('YmdH') . $id . date('is');  // 18 个字符
        return 'WX'.$number; // 20
    }

    public function getTransferStatusAttr($value) {
        return $value ? $value : 0;
    }

    public function user() {
        return $this->belongsTo('User', 'openid', 'openid');
    }

    /**
     * @param $origin_time
     * @param $page
     * @param int $listRows
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function selectPageRows( $origin_time ,$page, $listRows = 20 ) {
        $this->where('create_time', '<', $origin_time);
        return $this->order('create_time DESC')->page($page, $listRows)->cache(true)->select();
    }
}