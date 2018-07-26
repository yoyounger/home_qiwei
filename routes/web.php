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
    //所有商家列表接口
    Route::get('shops','ApiController@index');
    //指定商家信息接口
    Route::get('shop','ApiController@show');
    //用户注册接口
    Route::post('regist','ApiController@regist');
    //用户登录接口
    Route::post('loginCheck','ApiController@loginCheck');
    //短信验证功能接口
    Route::get('sms','ApiController@sms');
    //修改密码接口
    Route::post('changePassword','ApiController@changePassword');
    //重置密码接口
    Route::post('forgetPassword','ApiController@forgetPassword');
    //地址列表接口
    Route::get('addressList','ApiController@addressList');
    //添加地址接口
    Route::post('addAddress','ApiController@addAddress');
    //修改回显地址接口
    Route::get('address','ApiController@address');
    //修改保存地址接口
    Route::post('editAddress','ApiController@editAddress');
    //保存购物车接口
    Route::post('addCart','ApiController@addCart');
    //获取购物车列表接口
    Route::get('cart','ApiController@cart');
});