<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Basic extends Model
{
    protected $table = "sq_basic";
    /**
     * 查询网站基础配置
     * @return mixed
     */
    public function getBasic()
    {
        return DB::table($this->table)
            ->select(DB::raw('id, title, key_word, info, created_time, updated_time'))
            ->orderBy('id', 'desc')
            ->first();
    }


    /**
     * @param $page_size
     * 查询网站基础配置
     * @return mixed
     */
    public function getBasicList($page_size)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, key_word, info, created_time, updated_time'))
            ->paginate($page_size);
        $data = [
            'total'=>$results->total(),
            'currentPage'=>$results->currentPage(),
            'pageSize'=>$page_size,
            'list'=>[]
        ];
        foreach($results as $v){
            $data['list'][] = $v;
        }
        return  $data;
    }

    /**
     * @param $title
     * @param $key_word
     * @param $info
     * 新增网站基础配置
     * @return mixed
     */
    public function addBasic($title, $key_word, $info)
    {
        try{
            $insertArray = [
                'title' => $title,
                'key_word' => $key_word,
                'info' => $info,
                'created_time' => time(),
                'updated_time' => time(),
            ];
            $id = DB::table($this->table)->insertGetId($insertArray);
            $return = ['code'=>20000,'msg'=>'新增成功', 'data'=>[$id]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'新增失败', 'data'=>[$e->getMessage()]];
        }
        return $return;
    }

    /**
     * @param $id
     * @param $title
     * @param $key_word
     * @param $info
     * 修改网站基础配置
     * @return mixed
     */
    public function editBasic($id, $title, $key_word, $info)
    {
        try{
            $UpdateArray = [
                'title' => $title,
                'key_word' => $key_word,
                'info' => $info,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($UpdateArray);
            $return = ['code'=>20000,'msg'=>'编辑成功', 'data'=>[$id]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'编辑失败', 'data'=>[$e->getMessage()]];
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
            $return = ['code'=>40000,'msg'=>'删除失败', 'data'=>[$e->getMessage()]];
        }
        return $return;
    }
}
