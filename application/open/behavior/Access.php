<?php


namespace app\open\behavior;

use app\open\controller\Login;
use think\exception\HttpResponseException;
use app\open\controller\AbstractController;

class Access
{
    public function run(&$action) {
        list($controller, $method) = $action;

        $is_login = S('access')->chkIsLogin();
        if($controller instanceof Login) {
            if($is_login) {
                throw new HttpResponseException( redirect('/open.html') );
            }
        } elseif( $controller instanceof AbstractController) {
            if( !S('access')->chkIsLogin() ) {
                throw new HttpResponseException( redirect('/open/login.html') );
            }
        }
    }
}