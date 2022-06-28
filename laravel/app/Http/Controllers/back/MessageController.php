<?php


namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * 留言---留言列表
     * @param Request $request
     * @return mixed
     */
    public function messageList(Request $request)
    {
        $input = $request->all();
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $user_name = isset($input['user_name']) ? $input['user_name'] : '';//姓名
        $phone = isset($input['phone']) ? $input['phone'] : '';//电话
        $model_message = new Message();
        $return_data = $model_message->getList($page_size, $user_name, $phone);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 留言---处理留言
     * @param Request $request
     * @return mixed
     */
    public function editMessage(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $remarks = isset($input['remarks']) ? $input['remarks'] : '';//备注
        $status = isset($input['status']) ? $input['status'] : 0;//是否显示 1：已处理 0：未处理
        $model_message = new Message();
        $return_data = $model_message->editMessage($id, $remarks, $status);
        return response()->json($return_data);
    }

}
