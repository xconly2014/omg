<?php


namespace app\open\controller;

use app\common\exception\Factory;
use app\open\model\Admin;
use think\Controller;
use think\exception\RouteNotFoundException;
use think\Request;

class AbstractController extends Controller
{
    /**
     * @var \app\common\model\Menu
     */
    protected $cur_menu;

    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $menu = $this->menuData();
        if($this->cur_menu && stripos($this->cur_menu->getData('name'), '<i') === false) {
            $class_icon = $this->cur_menu->class_icon ? $this->cur_menu->class_icon : 'fa fa-dot-circle-o';
            $this->cur_menu->setAttr('name','<i class="' . $class_icon . '"></i> ' . $this->cur_menu->getData('name'));
        }

        $this->admin && $this->assign('admin', $this->getAdmin());
        $this->assign('menu', $menu);
        $this->assign('cur_menu', $this->cur_menu);
        return parent::fetch($template, $vars, $replace, $config);
    }

    public function json($data, $code = 0, $msg = '') {
        $this->result($data, $code, $msg, 'json');
    }

    /**
     * @var Admin
     */
    protected $admin;


    /**
     * 登录的账号
     * @return false|Admin
     */
    protected function getAdmin() {
        if(!$this->admin) {
            $this->admin = S('Access')->getAdmin();
        }
        if(!$this->admin) {
            $this->redirect('/open/login.html');
        }
        return $this->admin;
    }

    /**
     * 仅允许这些角色访问控制器
     * @param $roles
     */
    protected function allowRole($roles) {
        is_array($roles) || $roles = array($roles);

        $admin = $this->getAdmin();
        if(!in_array($admin->role, $roles)) {
            $this->error('Page Not Found!','/open.html');
        }
    }

    /**
     * @param string $name
     * @param null $default
     * @param bool $is_ciphertext 是否为密文
     * @param string $filter
     * @return mixed
     * @throws
     */
    public function param($name = '', $default = null, $is_ciphertext = false, $filter = '') {
        $value = $this->request->param($name, $default, $filter);
        if($is_ciphertext) {
            $value = S('Encipher')->decrypt($value);
            if(!$value) {
                throw Factory::failDecodeParam($name);
            }
        }
        return $value;
    }

    protected function menuData() {
        $rows = M('Menu')->order('priority DESC, id ASC')->select(); // ->cache(true, 3600)

        $menu = [];
        if( !empty($rows) ) {
            foreach ($rows as $row) {
                if(!$row->parent_id) {
                    $menu[ $row->id ] = [
                        'item' => $row,
                        'children' => []
                    ];
                }
            }
            $cur_menu = null;
            foreach ($rows as $row) {
                if($row->parent_id && isset($menu[ $row->parent_id ])) {
                    $row->setRelation('parent',  $menu[ $row->parent_id ]['item']);
                    $menu[ $row->parent_id ]['children'][] = $row;
                }

                if(!$this->cur_menu) {
                    if(stripos($this->request->path(), ltrim($row->getData('path'), "/")) === 0) {
                        $cur_menu = $row;
                    }
                    if($this->request->path() == $row->getData('path')) {
                        $cur_menu = $row;
                    }
                }
            }
            if($cur_menu) {
                $this->cur_menu = $cur_menu;
            }
        }

        return $menu;
    }

    protected function setCurMenu($data) {
        if(is_string($data)) {
            $data = ['name' => $data];
        }
        if(is_array($data)) {
            $data = M('Menu')->getInstance($data);
        }
        $this->cur_menu = $data;
    }
}