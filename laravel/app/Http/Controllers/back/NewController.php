<?php


namespace App\Http\Controllers\back;


use App\Http\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewController extends Controller
{

    /**
     * 后台—-新闻列表
     * @param Request $request
     * @return mixed
     */
    public function newList(Request $request)
    {
        $input = $request->all();
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $status = isset($input['status']) ? $input['status'] : 0;//状态 0：所有 1：显示
        $type = isset($input['type']) ? $input['type'] : 0;//类型 0: 所有  1:新闻资讯 2:公司动态
        $model_news = new News();
        $return_data = $model_news->getAllNews($page_size, $status, $type);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 新闻——新增新闻
     * @param Request $request
     * @return mixed
     */
    public function addNews(Request $request)
    {
        $input = $request->all();
        $thumbnail = isset($input['thumbnail']) ? $input['thumbnail'] : 0;//缩略图图片地址
        if(empty($thumbnail)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $NEWS_URL = env('NEWS_URL');
                $upload_data = $Common->uploadImages($tmp, 'news', $NEWS_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $thumbnail = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//新闻标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : '';//关键字
        $introduction = isset($input['introduction']) ? $input['introduction'] : '';//新闻简介
        $serial = isset($input['serial']) ? $input['serial'] : 1;//新闻序号
        $status = isset($input['status']) ? $input['status'] : 0;//新闻状态 1：正常 0： 无效
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//新闻内容
        $type = isset($input['type']) ? $input['type'] : 0;//类型 0: 所有  1:新闻资讯 2:公司动态
        $author = isset($input['author']) ? $input['author'] : '';//作者
        if(empty($thumbnail) || empty($title) || empty($admin_uid) || empty($content)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_news = new News();
        $return_data = $model_news->addNews($title, $type, $author, $key_word, $introduction, $thumbnail, $content, $serial, $status, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 新闻---修改新闻状态
     * @param Request $request
     * @return mixed
     */
    public function newsStatus(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $status = isset($input['status']) ? $input['status'] : 0;//是否显示 1：显示 0：隐藏
        $model_news = new News();
        $return_data = $model_news->editStatus($id, $status);
        return response()->json($return_data);
    }

    /**
     * 新闻——编辑新闻
     * @param Request $request
     * @return mixed
     */
    public function editNews(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;//数据ID
        $thumbnail = isset($input['thumbnail']) ? $input['thumbnail'] : 0;//缩略图图片地址
        if(empty($thumbnail)){
            $tmp = $request->file('file');
            if($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
                $Common = new CommonController();
                $NEWS_URL = env('NEWS_URL');
                $upload_data = $Common->uploadImages($tmp, 'news', $NEWS_URL);
            }else{
                return response()->json(['code'=>40000,'msg'=>'上传方式错误', 'data'=>[]]);
            }
            if($upload_data['code'] != 20000) return response()->json($upload_data);
            $thumbnail = $upload_data['file_name'];
        }
        $title = isset($input['title']) ? $input['title'] : '';//新闻标题
        $key_word = isset($input['key_word']) ? $input['key_word'] : '';//关键字
        $introduction = isset($input['introduction']) ? $input['introduction'] : '';//新闻简介
        $serial = isset($input['serial']) ? $input['serial'] : 1;//新闻序号
        $status = isset($input['status']) ? $input['status'] : 0;//新闻状态 1：正常 0： 无效
        $admin_uid = isset($input['admin_uid']) ? $input['admin_uid'] : 0;//上传人员ID
        $content = isset($input['content']) ? $input['content'] : 0;//新闻内容
        $type = isset($input['type']) ? $input['type'] : 0;//类型 0: 所有  1:新闻资讯 2:公司动态
        $author = isset($input['author']) ? $input['author'] : '';//作者
        if(empty($thumbnail) || empty($title) || empty($admin_uid) || empty($content) || empty($type))
        {
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $imgUrl = env('IMAGES_URL');
        $thumbnail = str_replace($imgUrl,'',$thumbnail);
        $model_news = new News();
        $return_data =  $model_news->editNews($id, $title, $type, $author, $key_word, $introduction, $thumbnail, $content, $serial, $status, $admin_uid);
        return response()->json($return_data);
    }

    /**
     * 新闻---批量删除数据
     * @param Request $request
     * @return mixed
     */
    public function batchDelNews(Request $request)
    {
        $input = $request->all();
        $ids = isset($input['ids']) ? $input['ids'] : []; //数据ID集合
        $model_news = new News();
        $return_data = $model_news->delIds($ids);
        return response()->json($return_data);
    }

    /**
     * 新闻---上传新闻内容图片
     * @param Request $request
     * @return mixed
     */
    public function uploadNewsImg(Request $request)
    {
        $input = $request->all();
        $tmp = $request->file('file');
        if ($request->isMethod('POST')) { //判断文件是否是 POST的方式上传
            $Common = new CommonController();
            $NEWS_URL = env('NEWS_URL');
            $upload_data = $Common->uploadImages($tmp, 'news', $NEWS_URL);
            return response()->json($upload_data);
        } else {
            return response()->json(['code' => 40000, 'msg' => '上传方式错误', 'data' => []]);
        }
    }
}
