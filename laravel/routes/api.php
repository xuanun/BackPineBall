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
    $router->post("/login", 'account\AccountController@login');//用户登录
    $router->group(['middleware'=>['authToken']],function () use ($router) {
        $router->post("/logout", 'account\AccountController@logout');//用户退出登录
        $router->post("/edit_password", 'account\AccountController@editPassword');//用户修改密码
    });
});
//官网首页
$router->group(['prefix' => 'website'], function () use ($router) {
    $router->group(['prefix' => 'index'], function () use ($router) {
        $router->post("/banner_list", 'website\IndexController@bannerList'); //banner图列表
        $router->post("/adv_list", 'website\IndexController@advList'); //服务优势列表
        $router->post("/case_list", 'website\IndexController@caseList'); //案例列表
        $router->post("/new_list", 'website\IndexController@newList'); //新闻列表
        $router->post("/add_message", 'website\IndexController@addMessage'); //新增留言
        $router->post("/new_r_list", 'website\IndexController@newRecommendList'); //推荐新闻
        $router->post("/new_detail", 'website\IndexController@newDetail'); //新闻详情
        $router->post("/case_detail", 'website\IndexController@caseDetail'); //案例详情
        $router->post("/friendly_link", 'website\IndexController@friendlyLink'); //友情链接
        $router->post("/basic_info", 'website\IndexController@basicInfo'); //网站基础配置
        $router->post("/around_new", 'website\IndexController@aroundNew'); //上一篇下一篇新闻
    });
});

//管理后台
$router->group(['prefix' => 'back'], function () use ($router) {
    //轮播图
    $router->group(['prefix' => 'banner'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/banner_list", 'back\BannerController@bannerList'); //banner图列表
            $router->post("/add_banner", 'back\BannerController@addBanner'); //新增Banner图
            $router->post("/edit_status", 'back\BannerController@bannerStatus'); //修改banner状态
            $router->post("/edit_banner", 'back\BannerController@editBanner'); //编辑Banner图
            $router->post("/batch_del_banner", 'back\BannerController@batchDelBanner'); //批量删除Banner图
        });
    });
    //服务优势
    $router->group(['prefix' => 'adv'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/adv_list", 'back\AdvantagesController@advList'); //服务优势列表
            $router->post("/add_adv", 'back\AdvantagesController@addAdv'); //新增服务优势
            $router->post("/edit_adv", 'back\AdvantagesController@editAdv'); //编辑服务优势
            $router->post("/batch_del_adv", 'back\AdvantagesController@batchDelAdv'); //批量删除服务优势
        });
    });
    //案例
    $router->group(['prefix' => 'case'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/case_list", 'back\CaseController@caseList'); //案例列表
            $router->post("/add_case", 'back\CaseController@addCase'); //新增案例
            $router->post("/edit_status", 'back\CaseController@caseStatus'); //修改案例状态
            $router->post("/edit_case", 'back\CaseController@editCase'); //编辑案例
            $router->post("/batch_del_case", 'back\CaseController@batchDelCase'); //批量删除案例
            $router->post("/upload_c_img", 'back\CaseController@uploadCaseImg'); //上传案例内容图片
        });
    });
    //新闻
    $router->group(['prefix' => 'news'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/new_list", 'back\NewController@newList'); //新闻列表
            $router->post("/add_news", 'back\NewController@addNews'); //新增新闻
            $router->post("/edit_status", 'back\NewController@newsStatus'); //修改案例状态
            $router->post("/edit_news", 'back\NewController@editNews'); //编辑新闻
            $router->post("/batch_del_news", 'back\NewController@batchDelNews'); //批量删除新闻
            $router->post("/upload_n_img", 'back\NewController@uploadNewsImg'); //上传新闻内容图片
        });
    });
    //留言
    $router->group(['prefix' => 'msg'], function () use ($router) {
        $router->post("/msg_list", 'back\MessageController@messageList'); //留言列表
        $router->post("/edit_message", 'back\MessageController@editMessage'); //处理留言
    });

    //友情链接
    $router->group(['prefix' => 'link'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/link_list", 'back\FriendlyLinkController@linkList'); //友情链接列表
            $router->post("/add_link", 'back\FriendlyLinkController@addLink'); //新增友情链接
            $router->post("/edit_link", 'back\FriendlyLinkController@editLink'); //修改友情链接
            $router->post("/batch_del_link", 'back\FriendlyLinkController@batchDelLink'); //批量删除数据
        });
    });

    //网站基础信息
    $router->group(['prefix' => 'basic'], function () use ($router) {
        $router->group(['middleware'=>['authToken']],function () use ($router) {
            $router->post("/basic_list", 'back\BasicController@BasicList'); //网站基础配置列表
            $router->post("/add_basic", 'back\BasicController@addBasic'); //新增网站基础配置
            $router->post("/edit_basic", 'back\BasicController@editBasic'); //修改网站基础配置
            $router->post("/batch_del_basic", 'back\BasicController@batchDelBasic'); //批量删除数据
        });
    });
    //下载远程图片
    $router->group(['prefix' => 'img'], function () use ($router) {
        $router->post("/down", 'website\IndexController@downImages'); //下载远程图片
    });
});
