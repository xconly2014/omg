<?php
namespace app\api\controller\docs;


class Home extends AbstractController
{
    public function index() {
        return $this->fetch();
    }
}