<?php


namespace App\Http\Controllers\back;


use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * 首页——新增轮播图
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
                $upload_data = $this->uploadImage($tmp);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $banner_url = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//轮播图标题
        $serial = isset($input['serial']) ? $input['serial'] : 1;//轮播图图片序号
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $state = isset($input['state']) ? $input['state'] : 0;//是否显示 1：显示 0：隐藏
        if(empty($banner_url) || empty($title) || empty($admin_uid)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_banner = new Banner();
        $return_data = $model_banner->addBanner($title, $banner_url, $serial, $state, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 上传图片
     * @param $tmp
     * @return mixed
     */
    public function uploadImage($tmp)
    {
        if(empty($tmp)) return ['code'=>40000,'msg'=>'文件流不存在', 'data'=>[]];
        if ($tmp->isValid())
        { //判断文件上传是否有效
            $FileType = $tmp->getClientOriginalExtension(); //获取文件后缀
            $FilePath = $tmp->getRealPath(); //获取文件临时存放位置
            $FileName = date('Ymd') . '/' . uniqid() . '.' . $FileType; //定义文件名
            Storage::disk('banner')->put($FileName, file_get_contents($FilePath)); //存储文件
            $BANNER_URL = env('BANNER_URL');
            $BANNERS_URL = env('BANNERS_URL');
            $data['url'] = $BANNERS_URL.$BANNER_URL. $FileName;
            $data['code'] = 20000;
            $data['file_name'] = $BANNER_URL.$FileName;
            return $data;
        }
        return ['code'=>40000,'msg'=>'文件不存在', 'data'=>[]];
    }
}
