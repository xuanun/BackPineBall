<?php


namespace App\Http\Controllers\back;


use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class FriendlyLinkController extends Controller
{
    /**
     * 后台——友情链接列表
     * @param Request $request
     * @return mixed
     */
    public function linkList(Request $request)
    {
        $input = $request->all();
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $model_link = new Link();
        $return_data = $model_link->getLinkList( $page_size);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 后台——新增友情链接
     * @param Request $request
     * @return mixed
     */
    public function addLink(Request $request)
    {
        $input = $request->all();
        $user_id = isset($input['user_id']) ? $input['user_id'] : 0;//关键词
        $key_word = isset($input['key_word']) ? $input['key_word'] : 0;//关键词
        $link = isset($input['link']) ? $input['link'] : 0;//友情链接
        if(empty($key_word) || empty($link)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_link = new Link();
        $return_data = $model_link->addLink($user_id, $key_word, $link);
        return response()->json($return_data);
    }

    /**
     * 后台——修改友情链接
     * @param Request $request
     * @return mixed
     */
    public function editLink(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//关键词
        $key_word = isset($input['key_word']) ? $input['key_word'] : 0;//关键词
        $link = isset($input['link']) ? $input['link'] : 0;//友情链接
        if(empty($key_word) || empty($link) || empty($id)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_link = new Link();
        $return_data = $model_link->editLink($id, $key_word, $link);
        return response()->json($return_data);
    }


    /**
     * 友情链接---批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelLink(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        if(empty($ids)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_link = new Link();
        $return_data = $model_link->delIds($ids);
        return response()->json($return_data);
    }
}
