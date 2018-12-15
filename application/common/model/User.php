<?php
namespace app\common\model;

class User extends  AbstractModel
{
    protected $autoWriteTimestamp = true;

    protected $field = ['openid', 'session_key', 'unionid', 'nickname', 'avatar', 'gender', 'province', 'city',
        'country', 'referrer', 'tel_prefix', 'tel_number', 'is_tel_bind', 'is_follow_our', 'last_login_time'];

    public function withdraws() {
        return $this->hasMany('withdraw', 'openid', 'openid');
    }

    public function findByOpenid($openid) {
        return M('user')->where('openid', $openid)->find();
    }

    public function setNicknameAttr($value) {
        return filterUtf8($value);
    }
}