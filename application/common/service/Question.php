<?php


namespace app\common\service;



class Question
{
    /**
     * @param \app\common\model\SubjectQuestion $question
     * @return array
     */
    public function simpleInfo(\app\common\model\SubjectQuestion $question) {
        return array(
            'id' => (string) $question->id,
            'title' => $question->title,
            'model_key' => $question->view_key,
        );
    }

    /**
     * @param \app\common\model\SubjectQuestion[] $rows
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
     * @param \app\common\model\SubjectQuestion $question
     * @return array
     */
    public function detailInfo(\app\common\model\SubjectQuestion $question) {
        $data = $this->simpleInfo($question);
        $opts = M('subjectQuestionOpt')->selectByQuestionID($question->id);
        $data['opts'] = $this->simpleOptList($opts);
        return $data;
    }

    /**
     * @param \app\common\model\SubjectQuestion[] $rows
     * @return array
     */
    public function detailList($rows) {
        $list = [];
        if(!empty($rows)) {
            foreach ($rows as $row) {
                $list[] = $this->detailInfo($row);
            }
        }
        return $list;
    }

    /**
     * @param \app\common\model\SubjectQuestionOpt $opt
     * @return array
     */
    public function simpleOptInfo(\app\common\model\SubjectQuestionOpt $opt) {
        return array(
            'value' => $opt->getData('value'),
            'detail' => $opt->detail,
            'pic' => $opt->pic,
        );
    }

    /**
     * @param \app\common\model\SubjectQuestionOpt[] $rows
     * @return array
     */
    public function simpleOptList($rows) {
        $list = [];
        if(!empty($rows)) {
            foreach ($rows as $row) {
                $list[] = $this->simpleOptInfo($row);
            }
        }
        return $list;
    }
}