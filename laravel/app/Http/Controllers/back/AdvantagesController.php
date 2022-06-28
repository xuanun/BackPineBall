<?php

namespace App\Http\Controllers\back;
use App\Http\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Models\Advantages;
use App\Models\Banner;
use Illuminate\Http\Request;

class AdvantagesController extends Controller
{
    /**
     * 后台—-服务优势
     * @param Request $request
     * @return mixed
     */
    public function advList(Request $request)
    {
        $input = $request->all();
        $model_advantages = new Advantages();
        $return_data = $model_advantages->getList();
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 服务优势——新增服务优势
     * @param Request $request
     * @return mixed
     */
    public function addAdv(Request $request)
    {
        $input = $request->all();
        $icon = isset($input['icon']) ? $input['icon'] : 0;//图标图片地址
        if (empty($icon)) {
            $tmp = $request->file('file');
            if ($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $ICON_URL = env('ICON_URL');
                $upload_data = $Common->uploadImages($tmp, 'icon', $ICON_URL);
            } else {
                return response()->json(['code' => 40000, 'msg' => '上传方式错误', 'data' => []]);
            }
            if ($upload_data['code'] != 20000) return response()->json($upload_data);
            $icon = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//服务优势标题
        $serial = isset($input['serial']) ? $input['serial'] : 1;//服务优势排序
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//服务优势内容
        if (empty($icon) || empty($title) || empty($admin_uid) || empty($content)) {
            return response()->json(['code' => 60000, 'msg' => '缺少参数', 'data' => []]);
        }
        $model_advantages = new Advantages();
        $return_data = $model_advantages->addAdvantages($title, $icon, $content, $admin_uid, $serial);
        return response()->json($return_data);
    }

    /**
     * 服务优势——编辑服务优势
     * @param Request $request
     * @return mixed
     */
    public function editAdv(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $icon = isset($input['icon']) ? $input['icon'] : 0;//图标图片地址
        if (empty($icon)) {
            $tmp = $request->file('file');
            if ($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $ICON_URL = env('ICON_URL');
                $upload_data = $Common->uploadImages($tmp, 'icon', $ICON_URL);
            } else {
                return response()->json(['code' => 40000, 'msg' => '上传方式错误', 'data' => []]);
            }
            if ($upload_data['code'] != 20000) return response()->json($upload_data);
            $icon = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//服务优势标题
        $serial = isset($input['serial']) ? $input['serial'] : 1;//服务优势排序
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//服务优势内容
        if (empty($icon) || empty($title) || empty($admin_uid) || empty($content)) {
            return response()->json(['code' => 60000, 'msg' => '缺少参数', 'data' => []]);
        }
        $imgUrl = env('IMAGES_URL');
        $icon = str_replace($imgUrl, '', $icon);
        $model_advantages = new Advantages();
        $return_data = $model_advantages->editAdvantages($id, $title, $icon, $content, $admin_uid, $serial);
        return response()->json($return_data);
    }

    /**
     * 服务优势---批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelAdv(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        $model_advantages = new Advantages();
        $return_data = $model_advantages->delIds($ids);
        return response()->json($return_data);
    }

}

