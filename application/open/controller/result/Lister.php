<?php


namespace app\open\controller\result;

use app\open\controller\AbstractController;

class Lister extends AbstractController
{
    public function index() {
        $subject = S('subject')->getSubject($this->request->route('id'));
        $this->setCurMenu('答案： <small>' . $subject->subject . '</small>');

        $rows = $this->selectRows($subject);
        $q_rows = M('subjectQuestion')->selectBySubjectID($subject->id);
        $questions = S('Question')->detailList($q_rows);

        $this->assign('subject', $subject);
        $this->assign('list', $rows);
        $this->assign('questions', $questions);
        return $this->fetch();
    }

    protected function selectRows($subject, $listRows = 20) {
        $page_config = [];
        $model = M('subjectResult');
        $model->where('subject_id', $subject->id);
        $model->order('id DESC');
        $paginate =  $model->paginate($listRows, false, $page_config);
        return $paginate;
    }
}