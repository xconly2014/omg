<?php


namespace app\open\controller\result;

use app\common\model\SubjectResult;
use app\open\controller\AbstractController;

class Edit extends AbstractController
{
    public function index() {
        $subject = S('subject')->getSubject($this->request->route('id'));
        $this->setCurMenu('编辑结果：<small>' . $subject->subject . '</small>');
        $result = $this->getResult($subject);

        $this->assign('subject', $subject);
        $this->assign('result', $result);
        return $this->fetch();
    }

    protected function getResult($subject) {
        if ($this->request->route('rid')) {
            $result = M('subjectResult')->findById($this->request->route('rid'));
        }
        if (!isset($result) || !$result) {
            $result = M('subjectResult');
        }
        return $result;
    }

    /**
     * POST 保存
     */
    public function create() {
        $post = $this->request->param();
        $message = $this->validateDataForCreate($post);
        if($message !== true) {
            $this->error($message);
        }

        if ($post['_id']) {
            $result = M('subjectResult')->findById($post['_id']);
        }
        if (!isset($result) || !$result) {
            $result = M('subjectResult');
        }

        $result->subject_id = $post['subject_id'];
        $result->view_key = $post['view_key'];
        $result->mask = $this->param('mask');
        $result->explain = $this->param('explain');

        $result->save();
        $this->success('保存成功'
            //,url('/open/result/edit', ['id'=> $this->request->route('id'), 'rid'=> $result->id]));
            //,url('/open/result', ['id'=> $this->request->route('id')])
        );
    }

    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            '_id' => 'number',
            'subject_id' => 'require|number|token',
            'view_key' => 'require|min:2|max:20',
            'mask' => 'max:500',
            'explain' => 'max:100'
        ]);
    }

    /**
     * 保存 mask
     */
    public function saveMask() {
        $result_id = $this->param('id');
        $mask = $this->param('mask');
        M('subjectResult')->where('id', $result_id)
            ->update(['mask' => $mask]);
        $this->json('', 1);
    }

    public function del() {
        $this->allowRole(1);
        $id = $this->param('id');
        if (is_numeric($id)) {
            $aff = SubjectResult::destroy($id);
            if ($aff) {
                $this->result('', 1);
            }
        }
        $this->result('', 0, '删除失败');
    }

    /**
     * 保存 code
     */
    public function saveCode() {
        $result_id = $this->param('id');
        $code = $this->param('code');
        M('subjectResult')->where('id', $result_id)
            ->update(['code' => $code]);
        $this->json('', 1);
    }
}
