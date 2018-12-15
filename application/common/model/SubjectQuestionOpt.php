<?php
namespace app\common\model;

use traits\model\SoftDelete;

class SubjectQuestionOpt extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['subject_id', 'question_id', 'type', 'value', 'detail', 'pic', 'delete_time'];

    public function question() {
        return $this->belongsTo('subjectQuestion');
    }

    public function getPicAttr($value) {
        return completeUrl($value);
    }

    public function selectByQuestionID($question_id) {
        return $this->where('question_id', $question_id)->select();
    }
}