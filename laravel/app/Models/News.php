<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    protected $table = "sq_news";
    const SHOW = 1; // 显示
    const HIDE = 0; //不显示
    /**
     * 查询新闻列表
     * @param $page_size
     * @param $status
     * @param $type
     * @return mixed
     */
    public function getAllNews($page_size, $status, $type)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, type, author, key_word, introduction, thumbnail, content, read_amount, serial, status, created_time, updated_time'));
        if($status){
            $results = $results->where('status',$status);
        }
        if($type){
            $results = $results->where('type',$type);
        }
        $results = $results
            ->orderBy('status','desc')
            ->orderBy('serial','asc')
            ->orderBy('updated_time', 'desc')
            ->paginate($page_size);

        $data = [
            'total'=>$results->total(),
            'currentPage'=>$results->currentPage(),
            'pageSize'=>$page_size,
            'list'=>[]
        ];

        $imgUrl = env('IMAGES_URL');
        foreach($results as $v){
            $v->file_name = $v->thumbnail;
            $v->thumbnail = $imgUrl.$v->thumbnail;
            $data['list'][] = $v;
        }
        return  $data;
    }

    /**
     * 查询新闻推荐列表
     * @param $new_id
     * @param $type
     * @param $limit
     * @return mixed
     */
    public function getRandNews($new_id, $type, $limit)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, type, author, key_word, introduction, thumbnail, content, read_amount, serial, status, created_time, updated_time'))
            ->where('id', '!=',$new_id);
        if($type)
            $results = $results->where('type', $type);
        $results = $results
            ->inRandomOrder($limit)
            ->get();

        $imgUrl = env('IMAGES_URL');
        $data = array();
        foreach($results as $v){
            $v->file_name = $v->thumbnail;
            $v->thumbnail = $imgUrl.$v->thumbnail;
            $data['list'][] = $v;
        }
        return  $data;
    }
    /**
     * 查询新闻详情
     * @param $new_id
     * @return mixed
     */
    public function getNewsDetail($new_id)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, type, author, key_word, introduction, thumbnail, content, read_amount, serial, status, created_time, updated_time'))
            ->where('id', $new_id)
            ->get();
        $imgUrl = env('IMAGES_URL');
        $data = array();
        foreach($results as $v){
            $v->file_name = $v->thumbnail;
            $v->thumbnail = $imgUrl.$v->thumbnail;
            $data['list'][] = $v;
        }
        return  $data;
    }

    /**
     * 新闻阅读量自增
     * @param $new_id
     */
    public function NewsIncrement($new_id)
    {
        DB::table($this->table)->where('id', $new_id)->increment('read_amount');
    }

    /**
     * @param $title
     * @param $type
     * @param $author
     * @param $key_word
     * @param $introduction
     * @param $thumbnail
     * @param $content
     * @param $serial
     * @param $status
     * @param $admin_uid
     * 新增新闻
     * @return mixed
     */
    public function addNews($title, $type, $author, $key_word, $introduction, $thumbnail, $content, $serial, $status, $admin_uid)
    {
        DB::beginTransaction();
        try{
            $insertArray = [
                'title' => $title,
                'type' => $type,
                'author' => $author,
                'key_word' => $key_word,
                'introduction' => $introduction,
                'thumbnail' => $thumbnail,
                'content' => $content,
                'read_amount' => 0,
                'serial' => $serial,
                'status' => $status,
                'admin_uid' => $admin_uid,
                'created_time' => time(),
                'updated_time' => time(),
            ];
            $id = DB::table($this->table)->insertGetId($insertArray);
            $return = ['code'=>20000,'msg'=>'新增成功', 'data'=>[$id]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'新增失败', 'data'=>[]];
        }
        DB::commit();
        return $return;
    }

    /**
     * 修改新闻状态
     * @param $id
     * @param $status
     * @return mixed
     */
    public function editStatus($id,  $status)
    {
        try{
            $UpdateArray = [
                'status' => $status,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($UpdateArray);
            $return = ['code'=>20000,'msg'=>'编辑成功', 'data'=>[]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'编辑失败', 'data'=>[]];
        }
        return $return;
    }
    /**
     * 修改新闻
     * @param $id
     * @param $type
     * @param $key_word
     * @param $author
     * @param $title
     * @param $introduction
     * @param $thumbnail
     * @param $content
     * @param $serial
     * @param $status
     * @param $admin_uid
     * @return mixed
     */
    public function editNews($id, $title, $type, $author, $key_word, $introduction, $thumbnail, $content, $serial, $status, $admin_uid)
    {
        try{
            $UpdateArray = [
                'title' => $title,
                'type' => $type,
                'author' => $author,
                'key_word' => $key_word,
                'introduction' => $introduction,
                'thumbnail' => $thumbnail,
                'content' => $content,
                'serial' => $serial,
                'status' => $status,
                'admin_uid' => $admin_uid,
                'created_time' => time(),
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($UpdateArray);
            $return = ['code'=>20000,'msg'=>'编辑成功', 'data'=>[]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'编辑失败', 'data'=>[]];
        }
        return $return;
    }

    /**
     * @param $ids
     * 批量删除数据
     * @return mixed
     */
    public function delIds($ids)
    {
        try{
            DB::table($this->table)
                ->whereIn('id', $ids)
                ->delete();
            $return = ['code'=>20000,'msg'=>'删除成功', 'data'=>[]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'删除失败', 'data'=>[]];
        }
        return $return;
    }

    /**
     * 查询新闻上一篇 或者下一篇
     * @param $new_id
     * @param $type
     * @param $is_up  1:上一篇  2:下一篇
     * @return mixed
     */
    public function getNews($new_id, $type, $is_up)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, type'))
            ->where('type', $type)
            ->orderBy('status','desc')
            ->orderBy('serial','asc')
            ->orderBy('updated_time', 'desc');
        //上一篇
        if($is_up == 1)
            $results = $results->where('id', '<', $new_id);
        //下一篇
        if($is_up == 2)
            $results = $results->where('id', '>', $new_id);
        return $results->first();
    }
}
