<?php


namespace app\api\service\subject;


use app\api\service\AbstractApiHandler;

class Lister extends AbstractApiHandler
{
    public $rule = [
        'page'  =>  'require|integer|>=:1',
        'page_rows' => 'integer|>=:1',
        'page_time' => 'require|integer',
        'sort' => 'number',
        'filter' => 'max:64',
        'debug' => 'number|in:0,1'
    ];

    public function api() {

        $data = ['top'=> []];
        if ($this->param('page') == 1) {
            $data['top'] = $this->selectTop();
        }
        $games = $this->selectPageList();
        $listData = $this->listData($games);
        $data = array_merge($data, $listData);
        $data['is_audit_during'] = M('config')->getItem('is_audit_during');
        return $data;
    }

    protected function selectTop() {
        $rows = $this->selectTopGames();
        return S('subject')->simpleList($rows);
    }

    protected function selectPageList() {
        $rows = $this->selectPageGames();
        if ($this->param('page', 1) == 1) {
            $hot_rows = $this->selectHotGames();
            shuffle($hot_rows);
            //$hot_rows = array_slice($hot_rows, 0, 4);
            $rows = array_merge($hot_rows, $rows);
        }
        return S('subject')->simpleList($rows);
    }

    /**
     * 热门
     * @return mixed
     */
    protected function selectHotGames() {
        $filter = $this->param('filter');
        $model = M('subject');
        if (!$this->param('debug')) {
            $model->publish();
        }
        $model->where('is_hot', 1);
        $model->where('is_top', 0);
        if ($filter) {
            $model->where('sid', '<>', $filter);
        }
        $model->order('publish_at DESC');
        $model->limit(64);
        return $model->cache(300)->select();
    }

    /**
     * 分页数据
     */
    protected function selectPageGames() {
        $sort = (int) $this->param('sort');
        $filter = $this->param('filter');

        $model = M('subject');
        if (!$this->param('debug')) {
            $model->publish();
        }
        $model->originPage(
            $this->param('page_time' ),
            $this->param('page', 1),
            $this->param('page_rows', 20)
        );

        switch ($sort) {
            case 2: // 热度
                $model->order('hit_num DESC');
                break;
            default:
                $model->order('publish_at DESC');
        }

        $model->where('is_hot', 0);
        $model->where('is_top', 0);
        if ($filter) {
            $model->where('sid', '<>', $filter);
        }

        return $model->cache(300)->select();
    }

    /**
     * 置顶数据
     * @return mixed
     */
    protected function selectTopGames() {
        $filter = $this->param('filter');
        $model = M('subject');
        $model->publish();
        $model->where('is_top', 1);
        if ($filter) {
            $model->where('sid', '<>', $filter);
        }
        return $model->cache(300)->select();
    }
}