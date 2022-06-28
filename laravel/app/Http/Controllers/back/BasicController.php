<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use App\Models\Basic;
use Illuminate\Http\Request;

class BasicController extends Controller
{
    /**
     * 后台——网站基础配置列表
     * @param Request $request
     * @return mixed
     */
    public function basicList(Request $request)
    {
        $input = $request->all();
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $model_basic = new Basic();
        $return_data = $model_basic->getBasicList($page_size);
        return response()->json(['code' => 20000, 'msg' => '请求成功', 'data' => $return_data]);
    }

    /**
     * 后台——新增网站基础配置
     * @param Request $request
     * @return mixed
     */
    public function addBasic(Request $request)
    {
        $input = $request->all();
        $title = isset($input['title']) ? $input['title'] : 0;//网站标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : 0;//网站关键字
        $info = isset($input['info']) ? $input['info'] : 0;//网站描述
        if (empty($key_word) || empty($info) || empty($title)) {
            return response()->json(['code' => 60000, 'msg' => '缺少参数', 'data' => []]);
        }
        $model_basic = new Basic();
        $return_data = $model_basic->addBasic($title, $key_word, $info);
        return response()->json($return_data);
    }

    /**
     * 后台——修改网站基础配置
     * @param Request $request
     * @return mixed
     */
    public function editBasic(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//ID
        $title = isset($input['title']) ? $input['title'] : 0;//网站标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : 0;//网站关键字
        $info = isset($input['info']) ? $input['info'] : 0;//网站描述
        if (empty($key_word) || empty($info) || empty($id) || empty($title)) {
            return response()->json(['code' => 60000, 'msg' => '缺少参数', 'data' => []]);
        }
        $model_basic = new Basic();
        $return_data = $model_basic->editBasic($id, $title, $key_word, $info);
        return response()->json($return_data);
    }


    /**
     * 网站基础配置--批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelBasic(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        if (empty($ids)) {
            return response()->json(['code' => 60000, 'msg' => '缺少参数', 'data' => []]);
        }
        $model_basic = new Basic();
        $return_data = $model_basic->delIds($ids);
        return response()->json($return_data);
    }
}
