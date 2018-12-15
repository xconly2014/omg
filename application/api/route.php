<?php
# api/route.php

use think\Route;

Route::group('docs', function() {
    Route::get('~','api/docs.home/index');
    Route::get('info','api/docs.info/index');
    Route::post('info/sign','api/docs.info/sign');
});