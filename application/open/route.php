<?php
# api/route.php

use think\Route;

Route::group('open', function() {
    Route::get('/$', 'open/index/index');
    Route::post('login', 'open/login/ajaxLogin');
    Route::group('subject', function () {
        Route::get('/$', 'open/subject.lister/index');

        Route::get('edit', 'open/subject.edit/index');
        Route::post('edit', 'open/subject.edit/create');
        Route::post('del', 'open/subject.edit/del');
        Route::post('publish', 'open/subject.edit/publish');
        Route::post('hot', 'open/subject.edit/hot');
        Route::post('top', 'open/subject.edit/top');
        Route::post('qrcode', 'open/subject.edit/qrcode');
    });

    Route::group('user', function () {
        Route::get('/$', 'open/user.lister/index');
    });

    Route::group('wx', function () {
        Route::get('cmsg', 'open/wx.customer/index');
        Route::get('cm_mass', 'open/wx.customerMass/index');
        Route::post('save_mass', 'open/wx.customerMass/saveMsg');
    });
    // 活动
    Route::get('activity/$', 'open/activity.activity/index');
    // 提现
    Route::get('withdraw/$', 'open/withdraw/index');
    Route::post('withdraw/withdraw', 'open/withdraw/withdraw');
    Route::post('withdraw/finish', 'open/withdraw/finish');
    Route::post('withdraw/reject', 'open/withdraw/reject');

    Route::get('setting', 'open/setting/index');
    Route::post('setting', 'open/setting/save');

    Route::get('question', 'open/question.lister/index');
    Route::post('question/edit', 'open/question.edit/create');
    Route::post('question/optEdit', 'open/question.optEdit/create');
    Route::post('question/optDel', 'open/question.optEdit/del');

    Route::get('result/id/:id', 'open/result.lister/index');
    Route::get('result/edit', 'open/result.edit/index');
    Route::post('result/edit', 'open/result.edit/create');
    Route::post('result/del', 'open/result.edit/del');
    Route::post('result/saveMask', 'open/result.edit/saveMask');
    Route::post('result/saveCode', 'open/result.edit/saveCode');
    Route::post('result/resourceEdit', 'open/result.resourceEdit/create');
    Route::post('result/resourceDel', 'open/result.resourceEdit/del');
});

