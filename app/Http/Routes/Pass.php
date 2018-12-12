<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/11
 * Time: 13:08
 */
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'pass'
],function (){
    Route::post('apply','PassController@apply');


    Route::group([
        'middleware' => ['token','auditor']
        //TODO : 测试
    ],function(){
        Route::get('getpasses/{limit}/{offset}','PassController@getPasses');
        Route::post('examine','PassController@examine');
    });


});
