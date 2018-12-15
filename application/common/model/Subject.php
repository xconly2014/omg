<?php
namespace app\common\model;

use traits\model\SoftDelete;

class Subject extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['sid','subject', 'qrcode', 'scene', 'cover', 'coverWidth', 'coverHeight', 'is_top', 'top_pic',
        'description', 'share_description', 'label', 'publish_at', 'hit_num', 'is_hot', 'ad'];

    protected static function init()
    {
        Subject::event('after_insert', function (Subject $entity) {
            if(!$entity->sid) {
                $entity->sid = $entity->idToAppId($entity->id);
            }
            $entity->save();
        });
        parent::init();
    }

    protected function idToAppId($id) {
        return 'S'.id2id($id, hexdec('SubjectModel'));
    }

    public function getCoverAttr($value) {
        return completeUrl($value);
    }

    public function getQrcode($value) {
        return completeUrl($value);
    }

    public function getTopPicAttr($value) {
        return completeUrl($value);
    }

    public function ads() {
        return $this->hasMany('subjectAd', 'subject_id');
    }

    public function getShareDescription($value, $data) {
        if (!$value && isset($data['description'])) {
            $value = $data['description'];
        }
        return $value;
    }

    /**
     * 是否发布
     * @param Query $query
     * @param int $publish_flag
     */
    public function scopePublish($query, $publish_flag = 1) {
        if( !is_null($publish_flag) ) {
            if($publish_flag) {
                $query->where('publish_at', '>', 0);
            } else {
                $query->where('publish_at', 0);
            }
        }
    }

    /**
     * 从 $origin_time 为起始点开始分页
     * @param Query $query
     * @param $origin_time
     * @param $page
     * @param int $listRows
     */
    public function scopeOriginPage($query, $origin_time, $page, $listRows = 20) {
        $query->where('publish_at', '<', $origin_time)->page($page, $listRows);
    }

    /**
     * @param $origin_time
     * @param $page
     * @param int $listRows
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function selectPageRows( $origin_time ,$page, $listRows = 20 ) {
        $this->where('create_time', '<', $origin_time);
        return $this->order('create_time DESC')->page($page, $listRows)->cache(true)->select();
    }

    public function findBySid( $sid ) {
        return $this->where('sid', $sid)->find();
    }
}