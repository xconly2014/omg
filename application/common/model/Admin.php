<?php


namespace app\common\model;

use think\Loader;
use traits\model\SoftDelete;

class Admin extends  AbstractModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    protected $field = ['role', 'email_account', 'password'];

    public function getByWhere(array $where) {
        if( isset($where['password']) ) {
            $where['password'] = $this->encryptPwd( $where['password'] );
        }
        return $this->where($where)->find();
    }

    public function getByEmailAccountPassword($account, $password) {
        $password = $this->encryptPwd($password);

        $this->where('email_account', $account)
            ->where('password', $password);
        return $this->find();
    }

    /**
     * 加密密码
     * @param $password
     * @param int $times 加密次数
     * @return mixed|string
     */
    private function encryptPwd($password, $times=2) {
        $value = md5($password);
        $hash = md5($value);
        $result = '';
        for($i=0; $i<32; $i++) {
            if(hexdec($hash[$i]) <= 7) {
                $result .= $value[$i];
            } else {
                $result .= strtoupper($value[$i]);
            }
        }
        $times--;
        return $times ? $this->encryptPwd($result, $times) : $result;
    }

    public function setPasswordAttr($value) {
        return $this->encryptPwd($value);
    }

    public function developer() {
        $model = Loader::parseClass('common', 'model', 'developer');
        return $this->belongsTo($model);
    }
}