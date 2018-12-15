<?php


namespace app\index\controller;

use think\Controller;

class Home extends Controller
{
    public function index() {
        return $this->fetch();
    }

    public function init() {
        $this->initReferrer();
        return 'end';
    }

    protected function initReferrer() {
        /*\Think\Db::execute("INSERT INTO `wx_omg`.`open_user_event` (
	`user_id`,
	`activity_id`,
	`event_hash`,
	`point`,
	`amount`,
	`attach`,
	`collect_at`,
	`withdraw_id`,
	`create_time`,
	`delete_time`
)
SELECT
	ou.id, 4, 'omg_referrer_v2', 2, 0, null, aue.collect_at, 0, aue.create_time, null
FROM eth_pstore.ap_user_event AS aue
INNER JOIN eth_pstore.ap_user AS au ON(au.id = aue.user_id)
INNER JOIN eth_pstore.ap_wx_user AS awu ON(au.id = awu.user_id)
INNER JOIN wx_omg.open_user AS ou ON(ou.openid = awu.openid)
WHERE
	aue.event_hash = 'omg_referrer_v2'
AND aue.delete_time IS NULL;");*/

        $rows = M('userEvent')->where('record_no', null)->select();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $row->record_no = $this->makeTradeNo($row->id);
                $row->save();
            }
        }
    }

    protected function makeTradeNo($id) {
        $id = str_pad($id % 10000, 4, "0", STR_PAD_LEFT); // 4
        $number = date('YmdH') . $id . date('is');  // 18 个字符
        return 'UA'.$number; // 20
    }
}