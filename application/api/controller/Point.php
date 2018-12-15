<?php


namespace app\api\controller;


class Point extends ApiController
{
    /**
     * @title 积分提现信息
     * @doc point/withdrawAlloc
     * @validate app\api\service\point\WithdrawAlloc
     */
    public function withdrawAlloc() {
        $data = S('point.withdrawAlloc')->api();
        return $this->response($data);
    }

    /**
     * @title 积分提现
     * @doc point/withdraw
     * @validate app\api\service\point\Withdraw
     */
    public function withdraw() {
        $data = S('point.withdraw')->api();
        return $this->response($data);
    }

    /**
     * @title 任务-邀请提现信息
     * @doc point/amountWithdrawAlloc
     * @validate app\api\service\point\AmountWithdrawAlloc
     */
    public function amountWithdrawAlloc() {
        $data = S('point.amountWithdrawAlloc')->api();
        return $this->response($data);
    }

    /**
     * @title 任务提现
     * @doc point/amountWithdraw
     * @validate app\api\service\point\AmountWithdraw
     */
    public function amountWithdraw() {
        $data = S('point.amountWithdraw')->api();
        return $this->response($data);
    }
}