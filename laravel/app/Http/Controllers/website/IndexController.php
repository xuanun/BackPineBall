<?php


namespace App\Http\Controllers\website;


use App\Http\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Models\Advantages;
use App\Models\Banner;
use App\Models\Basic;
use App\Models\Link;
use App\Models\Message;
use App\Models\News;
use App\Models\PineCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $type = isset($input['type']) ? $input['type'] : 0;//banner位置 0：所有 1：首页 2：开发服务
        $status = isset($input['status']) ? $input['status'] : 0;//状态 0：所有 1：显示
        $page_size = isset($input['page_size']) ? $input['page_size'] : 10;
        $page = isset($input['page']) ? $input['page'] : 1;
        $model_banner = new Banner();
        $return_data = $model_banner->getAllBanner($type, $page_size, $status);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-服务优势
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
     * 首页—-案例列表
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
     * 首页—-新闻列表
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
     * 首页—-现在联系
     * @param Request $request
     * @return mixed
     */
    public function addMessage(Request $request)
    {
        $input = $request->all();
        $user_name = isset($input['user_name']) ? $input['user_name'] : '';
        $phone = isset($input['phone']) ? $input['phone'] : '';
        $content = isset($input['content']) ? $input['content'] : '';
        if(empty($user_name) || empty($phone) || empty($content)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_message = new Message();
        $return_data = $model_message->addMessage($user_name, $phone, $content);
        return response()->json($return_data);
    }

    /**
     * 首页—-新闻推荐
     * @param Request $request
     * @return mixed
     */
    public function newRecommendList(Request $request)
    {
        $input = $request->all();
        $new_id = isset($input['new_id']) ? $input['new_id'] : 0;
        $type = isset($input['type']) ? $input['type'] : 0;
        $limit = isset($input['limit']) ? $input['limit'] : 4;
        $model_news = new News();
        $return_data = $model_news->getRandNews($new_id, $type, $limit);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-新闻上一篇 下一篇
     * @param Request $request
     * @return mixed
     */
    public function aroundNew(Request $request)
    {
        $input = $request->all();
        $new_id = isset($input['new_id']) ? $input['new_id'] : 0;
        $type = isset($input['type']) ? $input['type'] : 0;

        $model_news = new News();
        //上一篇
        $is_up = 1;
        $up_data = $model_news->getNews($new_id, $type, $is_up);
        //下一篇
        $is_up = 2;
        $down_data = $model_news->getNews($new_id, $type, $is_up);
        $return_data['up_data'] = $up_data;
        $return_data['down_data'] = $down_data;
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-新闻详情
     * @param Request $request
     * @return mixed
     */
    public function newDetail(Request $request)
    {
        $input = $request->all();
        $new_id = isset($input['new_id']) ? $input['new_id'] : '';//新闻ID
        if(empty($new_id)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_news = new News();
        $return_data = $model_news->getNewsDetail($new_id);
        $model_news->NewsIncrement($new_id);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-案例详情
     * @param Request $request
     * @return mixed
     */
    public function caseDetail(Request $request)
    {
        $input = $request->all();
        $case_id = isset($input['case_id']) ? $input['case_id'] : '';//案例ID
        if(empty($case_id)){
            return response()->json(['code'=>60000,'msg'=>'缺少参数', 'data'=>[]]);
        }
        $model_case = new PineCase();
        $return_data = $model_case->getCaseDetail($case_id);
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-友情链接
     * @param Request $request
     * @return mixed
     */
    public function friendlyLink(Request $request)
    {
        $model_Link = new Link();
        $return_data = $model_Link->getAllLink();
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }

    /**
     * 首页—-网站基础配置
     * @param Request $request
     * @return mixed
     */
    public function basicInfo(Request $request)
    {
        $model_basic = new Basic();
        $return_data = $model_basic->getBasic();
        return response()->json(['code'=>20000,'msg'=>'请求成功',  'data'=>$return_data]);
    }


    /**
     * 首页—-下载远程图片
     * @param Request $request
     * @return mixed
     */
    public function downImages(Request $request)
    {
        $input = $request->all();
        $url = isset($input['url']) ? $input['url'] : ''; //头像
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
//        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $filename = pathinfo($url, PATHINFO_BASENAME);

        Storage::disk('icon')->put($filename, file_get_contents($url)); //存储文件
        return  json_encode($filename);
    }
}
