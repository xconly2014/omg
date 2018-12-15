<?php
namespace app\common\model;

use traits\model\SoftDelete;

class SubjectResultResources extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['subject_id', 'result_id', 'view_key', 'type', 'detail'];

    public function result() {
        return $this->belongsTo('subjectResult');
    }

    public function getDetailAttr($value, $data) {
        if ($data['type'] === 'PIC') {
            $value = completeUrl($value);
        }
        return $value;
    }

    public function selectByResultID($result_id) {
        return $this->where('result_id', $result_id)->cache()->select();
    }
}