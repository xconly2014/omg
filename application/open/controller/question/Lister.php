<?php


namespace app\open\controller\question;

use app\open\controller\AbstractController;

class Lister extends AbstractController
{
    public function index() {
        $subject = S('subject')->getSubject($this->request->route('id'));
        $this->setCurMenu('问题<small>' . $subject->subject . '</small>');

        $rows = $this->selectRows($subject);
        $this->assign('subject', $subject);
        $this->assign('list', $rows);
        return $this->fetch();
    }

    protected function selectRows($subject, $listRows = 20) {
        $page_config = [];
        $model = M('subjectQuestion');
        $model->where('subject_id', $subject->id);
        $paginate =  $model->paginate($listRows, false, $page_config);
        return $paginate;
    }
}