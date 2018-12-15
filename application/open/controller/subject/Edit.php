<?php


namespace app\open\controller\subject;

use app\common\model\Subject;
use app\open\controller\AbstractController;

class Edit extends AbstractController
{
    /**
     * 页
     * @return mixed
     */
    public function index() {
        $this->setCurMenu('编辑主题');
        $subject = $this->getSubject();
        $this->assign('subject', $subject);
        return $this->fetch();
    }

    protected function getSubject() {
        if ($this->request->route('id')) {
            $subject = M('subject')->findBySid($this->request->route('id'));
            if ($subject) {
                $subject->coverDataUrl = S('upload')->getDataURL($subject->getData('cover'));
            }
        }
        if (!isset($subject) || !$subject) {
            $subject = M('subject');
        }
        return $subject;
    }

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
            $subject = M('subject')->findById($post['_id']);
        }
        if (!isset($subject) || !$subject) {
            $subject = M('subject');
        }

        $subject->coverWidth = $post['coverWidth'];
        $subject->coverHeight = $post['coverHeight'];
        $subject->subject = $post['subject'];
        $subject->description = $post['description'];
        isset($post['share_description']) && $subject->share_description = $post['share_description'];
        $subject->label =$post['label'];

        $subject->save();
        $subject->cover = S('upload')->moveToDir($post['cover'], 'subject', 'c_' . $subject->id);
        $subject->save();
        S('upload')->clean();
        $this->json([
            'id' => $subject->id
        ], 1);
    }

    protected function validateDataForCreate($data) {
        return $this->validate($data, [
            '_id' => 'number',
            'cover' => 'require|token',
            'coverWidth' => 'require|number',
            'coverHeight' => 'require|number',
            'subject' => 'require|min:2|max:100',
            'description' => 'require|min:2|max:500',
            'share_description' => 'min:2|max:500',
            'label' => 'require|min:2|max:20',
        ]);
    }

    /**
     * 删除
     */
    public function del() {
        $this->allowRole(1);
        $id = $this->param('id');
        if (is_numeric($id)) {
            $aff = Subject::destroy($id);
            if ($aff) {
                $this->result('', 1);
            }
        }
        $this->result('', 0, '删除失败');
    }

    /**
     * 发布
     */
    public function publish() {
        $sid = $this->param('sid');
        $flag = (int) $this->param('flag');
        $value = $flag ? time() : 0;
        M('subject')->where('sid', $sid)
            ->update(['publish_at' => $value]);
        $this->json('', 1);
    }

    /**
     * 热门
     */
    public function hot() {
        $sid = $this->param('sid');
        $flag = (int) $this->param('flag');
        $value = $flag ? 1 : 0;
        M('subject')->where('sid', $sid)
            ->update(['is_hot' => $value]);
        $this->json('', 1);
    }

    /**
     * 置顶图片
     */
    public function top() {
        $sid = $this->param('sid');
        $top_pic = $this->param('top_pic');
        M('subject')->where('is_top', 1)->update(['is_top' => 0]);

        $subject = S('subject')->getSubject($sid);
        $subject->is_top = 1;
        $subject->top_pic = S('upload')->moveToDir($top_pic, 'subject', 'cTop_' . $subject->id);
        $subject->save();

        $this->json('', 1);
    }

    /**
     * 生成二维码
     */
    public function qrcode() {
        $sid = $this->param('sid');
        $scene = $this->param('scene');
        try {
            $content = S('WxApi')->getWXACodeUnlimit([
                'scene' => $scene,
                'page' => 'pages/detailPage/detailPage',
                /*'width' => 430,
                'auto_color' => false,
                'line_color' => ['r'=>0,'g'=>0,'b'=>0],
                'is_hyaline' => false*/
            ]);
        } catch (\Exception $e) {
            return $this->json('', 0, $e->getMessage());
        }
        $dir = ROOT_PATH  . 'public/static/images/upload/subject/' ;
        $dir = str_replace('/', DS, $dir);
        $filename = 'qr_' .md5($sid) . '.png';
        $path = '/static/images/upload/subject/' . $filename . '?t=' . time();
        $res = file_put_contents($dir . $filename, $content, LOCK_EX);
        if ($res === false) {
            return $this->json('', 0, '写如图片失败');
        }
        M('subject')->where('sid', $sid)->update([
            'scene' => $scene,
            'qrcode' => $path
        ]);

        return $this->json([
            'url' => $path
        ], 1);

    }
}