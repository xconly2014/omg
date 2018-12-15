<?php
namespace app\common\model;

use traits\model\SoftDelete;

class SubjectQuestion extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['subject_id', 'title', 'view_key'];

    public function opts() {
        return $this->hasMany('subjectQuestionOpt', 'question_id');
    }

    public function selectBySubjectID($subject_id) {
        return $this->where('subject_id', $subject_id)->select();
    }
}