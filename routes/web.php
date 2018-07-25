<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('api')->group(function (){
    //所有商家列表
   Route::get('shops','ApiController@index');
   //指定商家信息
   Route::get('shop','ApiController@show');
});