<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/6
 * Time: 20:18
 */
Route::get('/', function () {
    return view('welcome');
});
include 'Routes/User.php';
include 'Routes/Pass.php';