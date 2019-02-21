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
    ],function(){
        Route::post('examine','PassController@examine');
        Route::post('reject','PassController@deletePasses');
    });


    Route::group([
        'middleware' => ['token','scanner']
    ],function(){
        Route::post('search','PassController@getInfoByCarNumber');
        Route::post('getQRcode','PassController@getQRCode');
        Route::get('like/{carNumber}','PassController@getInfoByLike');
    });


    Route::group([
        'middleware' => ['token']
    ],function(){
        Route::get('getpasses/{limit}/{offset}','PassController@getPasses');
        Route::get('getcheckedpasses/{limit}/{offset}','PassController@getCheckedPasses');
    });

});
