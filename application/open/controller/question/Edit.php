<?php


namespace app\open\controller\question;

use app\open\controller\AbstractController;

class Edit extends AbstractController
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
            $question = M('subjectQuestion')->findById($post['_id']);
        }
        if (!isset($question) || !$question) {
            $question = M('subjectQuestion');
        }
        $question->subject_id = $post['subject_id'];
        $question->title = $post['title'];
        $question->view_key = $post['view_key'];
        $question->save();

        $this->json([
            'id' => $question->id
        ], 1);
    }

    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            '_id' => 'number',
            'subject_id' => 'require|number',
            'title' => 'require|token|min:4|max:200',
            'view_key' => 'require|min:2|max:20',
        ]);
    }
}