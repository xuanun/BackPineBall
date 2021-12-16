<?php


namespace App\Http\Controllers\account;


use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    //测试接口
    public function index()
    {
        return response()->json(['code'=>20000,'msg'=>env('VERIFY_TOKEN').'****这是许师傅的测试接口',  'data'=>[]]);
    }
    //测试接口
    public function test()
    {
        return response()->json(['code'=>20000,'msg'=>env('VERIFY_TOKEN').'****这是一个测试接口',  'data'=>[]]);
    }
}
