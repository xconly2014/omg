<?php

namespace app\open\controller\subject;

use app\open\controller\AbstractController;

class Lister extends AbstractController
{
    public function index() {
        $rows = $this->selectRows();
        $this->assign('list', $rows);
        return $this->fetch();
    }

    protected function selectRows($listRows = 20) {
        $page_config = [];
        $model = M('subject');
        $model->order('id DESC');
        $paginate =  $model->paginate($listRows, false, $page_config);
        return $paginate;
    }
}