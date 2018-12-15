<?php


namespace app\open\controller\question;

use app\common\model\SubjectQuestionOpt;
use app\open\controller\AbstractController;

class OptEdit extends AbstractController
{
    /**
     * POST 保存
     */
    public function create() {
        $post = $this->request->param();
        $message = $this->validateDataForCreate($post);
        if($message !== true) {
            $this->json(token(), 0, $message);
        }

        if ($post['_id']) {
            $opt = M('subjectQuestionOpt')->findById($post['_id']);
        }
        if (!isset($opt) || !$opt) {
            $opt = M('subjectQuestionOpt');
        }
        $opt->subject_id = $post['subject_id'];
        $opt->question_id = $post['question_id'];
        $opt->setAttr('value', $post['value']);
        $opt->detail = isset($post['detail']) ? $post['detail'] : '';

        $opt->save();
        if (isset($post['pic']) && $post['pic'] ) {
            $opt->pic = S('upload')->moveToDir($post['pic'], 'subject', 'opt_' . $opt->id);
            $opt->save();
            S('upload')->clean();
        }

        $this->json([
            'id' => $opt->id
        ], 1);
    }

    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            '_id' => 'number',
            'subject_id' => 'require|number|token',
            'question_id' => 'require|number',
            'value' => 'require|number|>=:0',
            'detail' => 'max:200',
            'pic' => 'max:255',
        ]);
    }

    public function del() {
        $this->allowRole(1);
        $id = $this->param('id');
        if (is_numeric($id)) {
            $aff = SubjectQuestionOpt::destroy($id);
            if ($aff) {
                $this->result('', 1);
            }
        }
        $this->result('', 0, '删除失败');
    }
}