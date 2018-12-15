<?php


namespace app\index\controller;

use think\Controller;
use think\exception\HttpException;

class Index extends Controller
{
    public function index() {
        throw new HttpException(404, 'Invalid request');
    }
}