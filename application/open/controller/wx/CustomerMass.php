<?php

namespace app\open\controller\wx;

use app\open\controller\AbstractController;

/**
 * 群发客服消息
 * @package app\open\controller\wx
 */
class CustomerMass extends AbstractController
{
    public function index() {
        $this->setCurMenu('群发客服消息');
        $this->assign('count_openid', $this->countOpenid());
        $this->assign('count_msg_openid', $this->countNearestMsgOpenid());
        $this->assign('list', $this->selectRows());

        return $this->fetch();
    }

    /**
     *
     */
    public function saveMsg() {
        $post = $this->request->param();
        $message = $this->validateDataForSave($post);
        if($message !== true) {
            $this->json(token(), 0, $message);
        }
        $mass = M('CmMass')->getInstance();
        $mass->subject = $post['subject'];
        $mass->msgtype = $post['msgtype'];
        $mass->detail = [
            'content' => htmlentities($post['content'])
        ];
        $mass->save();
        $this->json([
            'id' => $mass->id
        ], 1);
    }

    protected function validateDataForSave($data) {
        return $this->validate($data, [
            'subject' => 'require|min:5|max:50',
            'msgtype' => 'require|in:text,image,link,miniprogrampage',
            'content' => 'requireIf:msgtype,text|min:5|max:250'
        ]);
    }

    protected function selectRows($listRows = 20) {
        $page_config = [];
        $model = M('CmMass');
        $model->order('id DESC');
        $paginate =  $model->paginate($listRows, false, $page_config);
        return $paginate;
    }

    /**
     * 总用户数
     * @return integer
     */
    protected function countOpenid() {
        return M('user')->count('id');
    }

    /**
     * 可接收用户数
     * @return integer
     */
    protected function countNearestMsgOpenid() {
        $res = M('CustomerMessage')
            ->where('action', 0)
            ->where('CreateTime', '>', time() - 86400 * 2 + 300)
            ->field('COUNT(DISTINCT FromUserName) AS cnt')
            ->select();
        return empty($res) ? 0 : $res[0]->cnt;
    }
}