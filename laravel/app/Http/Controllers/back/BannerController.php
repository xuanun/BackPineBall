<?php

namespace App\Http\Controllers\back;

use App\Http\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * 后台——轮播图
     * @param Request $request
     * @return mixed
     */
    public function bannerList(Request $request)
    {
        $input = $request->all();
        $type = isset($input['type']) ? $input['type'] : 0;//banner位置 0：所有 1：首页 2：开发服务
        $status = isset($input['status']) ? $input['status'] : 0;//状态 0：所有 1：显示
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $model_banner = new Banner();
        $return_data = $model_banner->getAllBanner($type, $page_size, $status);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 轮播图——新增轮播图
     * @param Request $request
     * @return mixed
     */
    public function addBanner(Request $request)
    {
        $input = $request->all();
        $banner_url = isset($input['banner_url']) ? $input['banner_url'] : 0;//轮播图图片地址
        if(empty($banner_url)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $BANNER_URL = env('BANNER_URL');
                $upload_data = $Common->uploadImages($tmp, 'banner', $BANNER_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $banner_url = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//轮播图标题
        $type = isset($input['type']) ? $input['type'] : '';//banner位置 1：首页 2：开发服务
        $url_type = isset($input['url_type']) ? $input['url_type'] : '';//跳转类型 0：不跳转 1：跳转页面
        $link_url = isset($input['link_url']) ? $input['link_url'] : '';//跳转链接
        $serial = isset($input['serial']) ? $input['serial'] : 1;//轮播图图片序号
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $state = isset($input['state']) ? $input['state'] : 0;//是否显示 1：显示 0：隐藏
        if(empty($banner_url) || empty($title) || empty($admin_uid)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_banner = new Banner();
        $return_data = $model_banner->addBanner($title, $type, $url_type, $link_url, $banner_url, $serial, $state, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 轮播图---修改轮播图状态
     * @param Request $request
     * @return mixed
     */
    public function bannerStatus(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $state = isset($input['state']) ? $input['state'] : 0;//是否显示 1：显示 0：隐藏
        $model_banner = new Banner();
        $return_data = $model_banner->editStatus($id, $state);
        return response()->json($return_data);
    }

    /**
     * 轮播图——编辑轮播图
     * @param Request $request
     * @return mixed
     */
    public function editBanner(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $banner_url = isset($input['banner_url']) ? $input['banner_url'] : '';//轮播图图片地址
        if(empty($banner_url)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $BANNER_URL = env('BANNER_URL');
                $upload_data = $Common->uploadImages($tmp,'banner', $BANNER_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $banner_url = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//轮播图标题
        $type = isset($input['type']) ? $input['type'] : '';//banner位置 1：首页 2：开发服务
        $url_type = isset($input['url_type']) ? $input['url_type'] : '';//跳转类型 0：不跳转 1：跳转页面
        $link_url = isset($input['link_url']) ? $input['link_url'] : '';//跳转链接
        $serial = isset($input['serial']) ? $input['serial'] : 1;//轮播图图片序号
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $state = isset($input['state']) ? $input['state'] : 0;//是否显示 1：显示 0：隐藏
        if(empty($banner_url) || empty($title) || empty($admin_uid)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $imgUrl = env('IMAGES_URL');
        $banner_url = str_replace($imgUrl,'',$banner_url);
        $model_banner = new Banner();
        $return_data =  $model_banner->editBannerInfo($id, $title, $type, $url_type, $link_url, $banner_url, $serial, $state, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 轮播图---批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelBanner(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        $model_banner = new Banner();
        $return_data = $model_banner->delIds($ids);
        return response()->json($return_data);
    }

}
