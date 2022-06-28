<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Link extends Model
{
    protected $table = "sq_link";

    /**
     * 查询全部友情链接
     * @return mixed
     */
    public function getAllLink()
    {
        return DB::table($this->table)
            ->select(DB::raw('id, key_word, link, created_time, updated_time'))
            ->get();
    }

    /**
     * @param $page_size
     * 查询全部友情链接
     * @return mixed
     */
    public function getLinkList($page_size)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, key_word, link, created_time, updated_time'))
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
     * @param $user_id
     * @param $key_word
     * @param $link
     * 新增友情链接
     * @return mixed
     */
    public function addLink($user_id, $key_word, $link)
    {
        try{
            $insertArray = [
                'user_id' => $user_id,
                'key_word' => $key_word,
                'link' => $link,
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
     * @param $key_word
     * @param $link
     * 修改友情链接
     * @return mixed
     */
    public function editLink($id, $key_word, $link)
    {
        try{
            $UpdateArray = [
                'key_word' => $key_word,
                'link' => $link,
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
