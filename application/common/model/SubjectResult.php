<?php
namespace app\common\model;

use traits\model\SoftDelete;

class SubjectResult extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['subject_id', 'view_key', 'mask', 'explain', 'code'];

    public function selectBySubjectID($subject_id) {
        return $this->where('subject_id', $subject_id)->cache()->select();
    }

    public function resources() {
        return $this->hasMany('subjectResultResources', 'result_id');
    }
}