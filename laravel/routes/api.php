<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
$router->group(['prefix' => 'account'], function () use ($router) {
    $router->group(['middleware'=>['authToken']],function () use ($router) {
        $router->get("/index", 'account\AccountController@index'); //测试接口
        $router->post("/index", 'account\AccountController@index'); //测试接口
        $router->post("/test", 'account\AccountController@test'); //测试接口
    });
//    $router->get("/index", 'account\AccountController@index'); //测试接口
//    $router->post("/test", 'account\AccountController@test'); //测试接口
});
$router->group(['prefix' => 'website'], function () use ($router) {
    $router->group(['prefix' => 'index'], function () use ($router) {
        $router->get("/banner_list", 'website\IndexController@bannerList'); //测试接口
        $router->post("/banner_list", 'website\IndexController@bannerList'); //测试接口
    });
});

$router->group(['prefix' => 'back'], function () use ($router) {
    $router->group(['prefix' => 'banner'], function () use ($router) {
        $router->post("/add_banner", 'back\BannerController@addBanner'); //新增Banner图
    });
});
