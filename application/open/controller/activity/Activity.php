<?php


namespace app\open\controller\activity;


use app\open\controller\AbstractController;

class Activity extends AbstractController
{
    public function index() {
        $this->allowRole(1);
        $list = $this->selectRows();

        $this->assign('list', $list);
        return $this->fetch();
    }

    protected function selectRows() {
        $listRows = 20;
        $fun_query = function () {
            $model = M('activity');
            return $model;
        };
        $model = $fun_query();

        $model->order('id DESC');
        return $model->paginate($listRows);
    }
}