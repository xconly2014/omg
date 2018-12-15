<?php


namespace app\api\service\subject;


use app\api\service\AbstractApiHandler;
use app\common\exception\Factory;

class Verdict extends AbstractApiHandler
{
    public $rule = [
        'sid'  =>  'require|alphaDash|max:32',
        'values' => 'max:1024',
        'result_id' => 'number'
    ];

    public function api() {
        $values = $this->decodeValues();
        $subject = S('subject')->getSubject($this->param('sid'));
        if ($this->param('result_id')) {
            $result = S('result')->getById($this->param('result_id'), $subject->id);
        } else {
            $results = M('subjectResult')->selectBySubjectID($subject->id);
            if (empty($results)) {
                throw Factory::resourceNotFound('result');
            }
            $result = $this->verdictResults($results, $values);
        }
        return S('result')->detailInfo($result);
    }

    /**
     * @param $results
     * @param $values
     * @return mixed
     */
    protected function verdictResults(array $results, array $values) {
        $result = $results[0];
        $iv = array();
        foreach ($values as $v) {
            $iv[] = $v['id'] . '-' . $v['value'];
        }
        foreach ($results as $index => $result) {
            $masks = explode(',', $result->mask);
            $diff = array_diff($masks, $iv);
            if (count($masks) !== count($diff)) {
                unset($results[ $index ]);
            }
        }
        if (!empty($results)) {
            $index = array_rand ($results);
            $result = $results[ $index ];
        }

        return $result;
    }

    protected function decodeValues() {
        try {
            $arr = is_array( $this->param('values') ) ?
                $this->param('values') :
                ( $this->param('values') ? json_decode($this->param('values'), true) : []);
            if (!empty($arr)) {
                $arr[0]['id'];
                $arr[0]['value'];
            }
        } catch (\Exception $e) {
            throw Factory::failDecodeParam('values');
        }
        return $arr;
    }
}