<?php

namespace app\open\controller\wx;

use app\open\controller\AbstractController;

class Customer extends AbstractController
{
    public function index() {
        /*$rows = $this->selectRows();
        $this->assign('list', $rows);*/
        return $this->fetch();
    }

    protected function selectRows($listRows = 20) {
        $page_config = [];
        $model = M('CustomerMessage');
        $model->order('id DESC');
        $paginate =  $model->paginate($listRows, false, $page_config);
        return $paginate;
    }
}