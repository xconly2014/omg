<?php


namespace app\open\controller\result;

use app\common\model\SubjectResultResources;
use app\open\controller\AbstractController;

class ResourceEdit extends AbstractController
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
            $resource = M('subjectResultResources')->findById($post['_id']);
        }
        if (!isset($resource) || !$resource) {
            $resource = M('subjectResultResources');
        }
        $resource->subject_id = $post['subject_id'];
        $resource->result_id = $post['result_id'];
        $resource->view_key = $post['view_key'];
        $resource->setAttr('type', $post['type']);
        $resource->detail = $post['detail'];
        $resource->save();
        if ( $post['type'] === 'PIC') {
            $resource->detail = S('upload')->moveToDir($post['detail'], 'subject', 'rec_' . $resource->id);
            $resource->save();
            S('upload')->clean();
        }

        $this->json([
            'id' => $resource->id
        ], 1);
    }

    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            '_id' => 'number',
            'subject_id' => 'require|number|token',
            'result_id' => 'require|number',
            'view_key' => 'require|min:2|max:20',
            'type' => 'require|in:TXT,PIC',
            'detail' => 'require|max:200',
        ]);
    }

    public function del() {
        $this->allowRole(1);
        $id = $this->param('id');
        if (is_numeric($id)) {
            $aff = SubjectResultResources::destroy($id);
            if ($aff) {
                $this->result('', 1);
            }
        }
        $this->result('', 0, '删除失败');
    }
}