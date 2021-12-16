<?php


namespace App\Http\Controllers\website;


use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * 首页——轮播图
     * @param Request $request
     * @return mixed
     */
    public function bannerList(Request $request)
    {
        $input = $request->all();
        $model_banner = new Banner();
        $return_data = $model_banner->getAllBanner();
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }
}
