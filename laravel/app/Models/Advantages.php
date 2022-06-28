<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Advantages extends Model
{
    protected $table = "sq_advantages";
    /**
     * 查询服务优势列表
     * @return mixed
     */
    public function getList()
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, icon, content, serial, created_time, updated_time'))
            ->orderBy('serial','asc')
            ->orderBy('updated_time', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $imgUrl = env('IMAGES_URL');
        $data = array();
        foreach($results as $v){
            $v->file_name = $v->icon;
            $v->icon = $imgUrl.$v->icon;
            $data['list'][] = $v;
        }
        return  $data;
    }

    /**
     * @param $title
     * @param $icon
     * @param $content
     * @param $admin_uid
     * @param $serial
     * 新增服务优势
     * @return mixed
     */
    public function addAdvantages($title, $icon, $content, $admin_uid, $serial)
    {
        DB::beginTransaction();
        try{
            $insertArray = [
                'title' => $title,
                'icon' => $icon,
                'content' => $content,
                'admin_uid' => $admin_uid,
                'serial' => $serial,
                'updated_time' => time(),
                'created_time' => time(),
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
     * 修改服务优势
     * @param $id
     * @param $title
     * @param $icon
     * @param $content
     * @param $admin_uid
     * @param $serial
     * @return mixed
     */
    public function editAdvantages($id, $title, $icon, $content, $admin_uid, $serial)
    {
        try{
            $UpdateArray = [
                'title' => $title,
                'icon' => $icon,
                'content' => $content,
                'admin_uid' =>$admin_uid,
                'serial' => $serial,
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
