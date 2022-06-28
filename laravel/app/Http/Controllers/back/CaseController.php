<?php


namespace App\Http\Controllers\back;


use App\Http\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Models\PineCase;
use Illuminate\Http\Request;

class CaseController extends Controller
{

    /**
     * 后台—-案例列表
     * @param Request $request
     * @return mixed
     */
    public function caseList(Request $request)
    {
        $input = $request->all();
        $type = isset($input['type']) ? $input['type'] : 0;//案例列表 0：所有 1：app 2：小程序 3：WEB
        $status = isset($input['status']) ? $input['status'] : 0;//案例列表 0：所有 1：正常
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $model_case = new PineCase();
        $return_data = $model_case->getAllCase($page_size, $type, $status);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 案例——新增案例
     * @param Request $request
     * @return mixed
     */
    public function addCase(Request $request)
    {
        $input = $request->all();
        $thumbnail = isset($input['thumbnail']) ? $input['thumbnail'] : 0;//缩略图图片地址
        if(empty($thumbnail)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $CASE_URL = env('CASE_URL');
                $upload_data = $Common->uploadImages($tmp, 'case', $CASE_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $thumbnail = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//案例标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : '';//关键字
        $introduction = isset($input['introduction']) ? $input['introduction'] : '';//案例简介
        $type = isset($input['type']) ? $input['type'] : '';//案例类型 1：app 2：小程序 3：网站$系统定制 4:物联网解决方案
        $serial = isset($input['serial']) ? $input['serial'] : 1;//案例序号
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//案例内容
        $status = isset($input['status']) ? $input['status'] : 0;//是否显示 1：显示 0：隐藏
        if(empty($thumbnail) || empty($title) || empty($admin_uid) || empty($content)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_case = new PineCase();
        $return_data = $model_case->addCase($title, $key_word, $introduction, $type, $thumbnail, $content, $serial, $status, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 案例---修改案例状态
     * @param Request $request
     * @return mixed
     */
    public function caseStatus(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $status = isset($input['status']) ? $input['status'] : 0;//是否显示 1：显示 0：隐藏
        $model_case = new PineCase();
        $return_data = $model_case->editStatus($id, $status);
        return response()->json($return_data);
    }
    /**
     * 案例——编辑案例
     * @param Request $request
     * @return mixed
     */
    public function editCase(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $thumbnail = isset($input['thumbnail']) ? $input['thumbnail'] : 0;//缩略图图片地址
        if(empty($thumbnail)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $CASE_URL = env('CASE_URL');
                $upload_data = $Common->uploadImages($tmp, 'case', $CASE_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $thumbnail = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//案例标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : '';//关键字
        $introduction = isset($input['introduction']) ? $input['introduction'] : '';//案例简介
        $type = isset($input['type']) ? $input['type'] : '';//案例类型 1：app 2：小程序 3：WEB
        $serial = isset($input['serial']) ? $input['serial'] : 1;//案例序号
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//案例内容
        $status = isset($input['status']) ? $input['status'] : 0;//是否显示 1：显示 0：隐藏
        if(empty($thumbnail) || empty($title) || empty($admin_uid) || empty($content))
        {
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $imgUrl = env('IMAGES_URL');
        $thumbnail = str_replace($imgUrl,'',$thumbnail);
        $model_case = new PineCase();
        $return_data = $model_case->editCase($id, $title, $key_word, $introduction, $type, $thumbnail, $content, $serial, $status, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 案例---批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelCase(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        $model_case = new PineCase();
        $return_data = $model_case->delIds($ids);
        return response()->json($return_data);
    }

    /**
     * 案例---上传案例内容图片
     * @param Request $request
     * @return mixed
     */
    public function uploadCaseImg(Request $request)
    {
        $input = $request->all();
        $tmp = $request->file('file');
        if ($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
            $Common = new CommonController();
            $CASE_URL = env('CASE_URL');
            $upload_data = $Common->uploadImages($tmp, 'case', $CASE_URL);
            return response()->json($upload_data);
        } else {
            return response()->json(['code' => 40000, 'msg' => '上传方式错误', 'data' => []]);
        }
    }
}
