<?php


namespace app\api\controller\docs;


class Login extends AbstractController
{
    public function index() {
        return $this->fetch();
    }
}