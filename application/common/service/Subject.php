<?php
namespace app\common\service;


use app\common\exception\Factory;

class Subject
{
    /**
     * @param $sid
     * @return mixed
     * @throws \app\common\exception\CodeException
     */
    public function getSubject($sid) {
        $entity = M('subject')->findBySid($sid);
        if(!$entity) {
            throw Factory::resourceNotFound('subject');
        }
        return $entity;
    }

    /**
     * @param \app\common\model\Subject $subject
     * @return array
     */
    public function simpleInfo(\app\common\model\Subject $subject) {
        $data = array(
            'sid' => $subject->sid,
            'subject' => $subject->subject,
            'cover' => $subject->cover,
            'coverWidth' => $subject->coverWidth,
            'coverHeight' => $subject->coverHeight,
            'top_pic' => $subject->top_pic,
            'description' => $subject->description,
            'share_description' => $subject->share_description,
            'label' => $subject->label,
            'is_hot' => $subject->is_hot
        );
        return $data;
    }

    /**
     * @param \app\common\model\Subject[] $rows
     * @return array
     */
    public function simpleList($rows) {
        $list = [];
        if(!empty($rows)) {
            foreach ($rows as $row) {
                $list[] = $this->simpleInfo($row);
            }
        }
        return $list;
    }

    /**
     * @param \app\common\model\Subject $subject
     * @return array
     */
    public function detailInfo(\app\common\model\Subject $subject) {
        $data = $this->simpleInfo($subject);
        $questions = M('subjectQuestion')->selectBySubjectID($subject->id);
        $data['questions'] = S('Question')->detailList($questions);

        return $data;
    }
}