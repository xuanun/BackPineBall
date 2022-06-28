<?php


namespace App\Http\Common;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    /**
     * 上传轮播图
     * @param $tmp
     * @param $folder
     * @param $banner_url
     * @return mixed
     */
    public function uploadImages($tmp, $folder, $banner_url)
    {
        if(empty($tmp)) return ['code'=>40000,'msg'=>'文件流不存在', 'data'=>[]];
        if ($tmp->isValid())
        { //判断文件上传是否有效
            $FileType = $tmp->getClientOriginalExtension(); //获取文件后缀
            $FilePath = $tmp->getRealPath(); //获取文件临时存放位置
            $FileName = date('Ymd') . '/' . uniqid() . '.' . $FileType; //定义文件名
            Storage::disk($folder)->put($FileName, file_get_contents($FilePath)); //存储文件
            $imgUrl = env('IMAGES_URL');
            $data['url'] = $imgUrl.$banner_url.$FileName;
            $data['code'] = 20000;
            $data['file_name'] = $banner_url.$FileName;
            return $data;
        }
        return ['code'=>40000,'msg'=>'文件不存在', 'data'=>[]];
    }

}
