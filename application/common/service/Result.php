<?php
namespace app\common\service;


use app\common\exception\Factory;

class Result
{
    /**
     * @param $id
     * @param $subject_id
     * @return mixed
     * @throws \app\common\exception\CodeException
     */
    public function getById($id, $subject_id = false) {
        $query = M('subjectResult')->where('id', $id);
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }
        $entity = $query->cache()->find();
        if(!$entity) {
            throw Factory::resourceNotFound('result');
        }
        return $entity;
    }

    public function simpleInfo(\app\common\model\SubjectResult $result) {
        $code = '';
        if ($result->code) {
            @$code = json_decode($result->code, true);
            is_array($code) || $code = '';
        }
        return array(
            'result_id' => $result->id,
            'model_key' => $result->view_key,
            'code' => $code
        );
    }

    /**
     * @param \app\common\model\SubjectResult $result
     * @return array
     */
    public function detailInfo(\app\common\model\SubjectResult $result) {
        $data = $this->simpleInfo($result);
        $resources = M('subjectResultResources')->selectByResultID($result->id);
        $data['resources'] = $this->simpleResourcesList($resources);
        return $data;
    }


    /**
     * @param \app\common\model\subjectResultResources $resource
     * @return array
     */
    public function simpleResourcesInfo(\app\common\model\subjectResultResources $resource) {
        $data = array(
            'type' => $resource->getData('type'),
            'detail' => $resource->detail,
        );
        return $data;
    }
    /**
     * @param \app\common\model\subjectResultResources[] $rows
     * @return array
     */
    public function simpleResourcesList($rows) {
        $list = [];
        if(!empty($rows)) {
            foreach ($rows as $row) {
                $list[$row->view_key] = $this->simpleResourcesInfo($row);
            }
        }
        return $list;
    }
}