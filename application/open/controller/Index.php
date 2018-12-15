<?php

namespace app\open\controller;


class Index extends AbstractController
{
    public function index() {
        return $this->fetch();
    }
}