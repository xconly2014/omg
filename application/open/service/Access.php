<?php


namespace app\open\service;

use app\common\model\Admin;
use think\Session;

class Access
{
    /**
     * @param $account
     * @param $password
     * @param $expire
     * @return string|true;
     */
    public function doLogin($account, $password, $expire = 0) {
        $admin = M('admin')->getByEmailAccountPassword($account, $password);
        if(!$admin) {
            return 'Incorrect account or password.';
        }
        $this->saveAdminToSession($admin, $expire);
        return true;
    }

    /**
     * 保存登录账号信息
     * @param Admin $admin
     * @param int $expire
     */
    protected function saveAdminToSession(Admin $admin, $expire = 0) {
        /*$expire = $expire > 0 ?
            min($expire, ini_get('session.gc_maxlifetime'))
            : ini_get('session.gc_maxlifetime');*/

        $expire_at = $expire ? time() + $expire : 0;

        $data = [
            'admin_id' => $admin->id,
            'developer_id' => $admin->developer_id,
            'role' => $admin->role,
            'expire_at' => $expire_at
        ];
        Session::set('admin', $data, 'open');
    }

    /**
     * 是否已经登录
     * @return bool
     */
    public function chkIsLogin() {
        $admin = Session::get('admin', 'open');
        if($admin) {
            return $admin['expire_at'] == 0 || $admin['expire_at'] > time();
        }
        return false;
    }

    /**
     * @return Admin|false
     */
    public function getAdmin() {
        $data = Session::get('admin', 'open');
        return M('admin')->getById($data['admin_id']);
    }
}