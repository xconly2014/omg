<?php


namespace app\open\controller;


class Withdraw extends AbstractController
{
    public function index() {
        $this->allowRole(1);
        $list = $this->selectRows();

        $this->assign('list', $list);
        return $this->fetch();
    }

    protected function selectRows() {
        $listRows = 50;
        $fun_query = function () {
            $model = M('withdraw');
            return $model;
        };
        $model = $fun_query();
        $model->with('user');

        $model->order('id DESC');
        return $model->paginate($listRows);
    }

    /**
     * 微信提现
     */
    public function withdraw() {
        $this->allowRole(1);
        $withdraw = M('withdraw')->findById($this->request->post('id'));
        if ($withdraw->transfer_status != 1) {
            S('withdraw')->sendToWx($withdraw->openid, $withdraw);
        }
        $data = S('withdraw')->simpleInfo($withdraw);
        return $this->json($data, 1);
    }

    /**
     * 直接完成
     */
    public function finish() {
        $this->allowRole(1);
        $withdraw = M('withdraw')->findById($this->request->post('id'));
        if ($withdraw->transfer_status != 1) {
            $withdraw->transfer_status = 1;
            $withdraw->transfer_at = time();
            $withdraw->save();
        }
        return $this->json([
            'transfer_at' => date('Y-m-d H:i', $withdraw->transfer_at)
        ], 1);
    }

    /**
     * 拒绝
     */
    public function reject() {
        $this->allowRole(1);
        $this->validateDataForReject();

        $withdraw = M('withdraw')->findById($this->request->post('id'));
        if ($withdraw->transfer_status == 0) {
            $withdraw->transfer_status = 2;
            $withdraw->transfer_at = time();
            $withdraw->transfer_err = $this->request->post('reason');
            $withdraw->save();
        }
        return $this->json([
            'token' => token(),
            'transfer_at' => date('Y-m-d H:i', $withdraw->transfer_at),
            'transfer_err' => $withdraw->transfer_err
        ], 1);
    }

    protected function validateDataForReject() {
        $message = $this->validate($this->request->post(), [
            'id' => 'require|number|token',
            'reason' => 'require|min:4|max:200'
        ]);
        if($message !== true) {
            $this->json([
                'token' => token()
            ], 0, $message);
        }
    }
}