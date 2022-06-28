<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PineCase extends Model
{
    protected $table = "sq_case";
    /**
     * 查询案例列表
     * @param $page_size
     * @param $type
     * @param $status
     * @return mixed
     */
    public function getAllCase($page_size, $type, $status)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, key_word, introduction, type, thumbnail, content, serial, status, created_time, updated_time'));
        if($type){
            $results = $results->where('type',$type);
        }
        if($status){
            $results = $results->where('status',$status);
        }
        $results =  $results
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
     * 查询案例详情
     * @param $case_id
     * @return mixed
     */
    public function getCaseDetail($case_id)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, key_word, introduction, type, thumbnail, content, serial, status, created_time, updated_time'))
            ->where('id', $case_id)
            ->get();

        $data = array();
        $imgUrl = env('IMAGES_URL');
        foreach($results as $v){
            $v->file_name = $v->thumbnail;
            $v->thumbnail = $imgUrl.$v->thumbnail;
            $data['list'][] = $v;
        }
        return  $data;
    }


    /**
     * @param $title
     * @param $key_word
     * @param $introduction
     * @param $type
     * @param $thumbnail
     * @param $content
     * @param $serial
     * @param $status
     * @param $admin_uid
     * 新增案例
     * @return mixed
     */
    public function addCase($title, $key_word, $introduction, $type, $thumbnail, $content, $serial, $status, $admin_uid)
    {
        DB::beginTransaction();
        try{
            $insertArray = [
                'title' => $title,
                'key_word' => $key_word,
                'introduction' => $introduction,
                'type' => $type,
                'thumbnail' => $thumbnail,
                'content' => $content,
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
     * 修改案例状态
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
     * 修改案例
     * @param $id
     * @param $key_word
     * @param $title
     * @param $introduction
     * @param $type
     * @param $thumbnail
     * @param $content
     * @param $serial
     * @param $status
     * @param $admin_uid
     * @return mixed
     */
    public function editCase($id, $title, $key_word, $introduction, $type, $thumbnail, $content, $serial, $status, $admin_uid)
    {
        try{
            $UpdateArray = [
                'title' => $title,
                'key_word' => $key_word,
                'introduction' => $introduction,
                'type' => $type,
                'thumbnail' => $thumbnail,
                'content' => $content,
                'serial' => $serial,
                'status' => $status,
                'admin_uid' => $admin_uid,
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
}
