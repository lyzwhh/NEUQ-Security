<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/9
 * Time: 17:11
 */
use Illuminate\Support\Facades\Route;
Route::group([
    'prefix' => 'user'
],function (){
    Route::post('register','UserController@register');
    Route::post('loginweb','UserController@login');  //wen端需要登陆两种账号
    Route::post('loginapp','UserController@login')->middleware('scanner');


    Route::group([
        'middleware' => ['token','leader']
    ],function(){
//        Route::get('getNormalScannerList','UserController@getNormalScannerList');
        Route::post('resetNormalScannerPassword','UserController@resetNormalScannerPassword');

    });

});