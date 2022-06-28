<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Banner extends Model
{
    protected $table = "sq_banner";
    const SHOW = 1; // 显示
    const HIDE = 0; //不显示
    /**
     * @param $type
     * @param $page_size
     * @param $status
     * 查询轮播图列表
     * @return mixed
     */
    public function getAllBanner($type, $page_size, $status)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, title, type, url_type, link_url, banner_url, serial, state, created_time, updated_time'));
        if($type){
            $results = $results->where('type',$type);
        }
        if($status){
            $results = $results->where('state',$status);
        }
        $results = $results
            ->orderBy('state','desc')
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
            $v->file_name = $v->banner_url;
            $v->banner_url = $imgUrl.$v->banner_url;
            $data['list'][] = $v;
        }
        return  $data;
    }

    /**
     * @param $title
     * @param $type
     * @param $url_type
     * @param $link_url
     * @param $banner_url
     * @param $serial
     * @param $state
     * @param $admin_uid
     * 新增轮播图
     * @return mixed
     */
    public function addBanner($title, $type, $url_type, $link_url, $banner_url, $serial, $state, $admin_uid)
    {
        DB::beginTransaction();
        try{
            $insertArray = [
                'title' => $title,
                'type' => $type,
                'url_type' => $url_type,
                'link_url' => $link_url,
                'banner_url' => $banner_url,
                'serial' => $serial,
                'state' => $state,
                'admin_uid' => $admin_uid,
                'created_time' => time(),
                'updated_time' => time(),
            ];
            $id = DB::table($this->table)->insertGetId($insertArray);
            $return = ['code'=>20000,'msg'=>'新增成功', 'data'=>[$id]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'新增失败', 'data'=>[$e->getMessage()]];
        }
        DB::commit();
        return $return;
    }

    /**
     * 修改轮播图信息
     * @param $id
     * @param $title
     * @param $type
     * @param $url_type
     * @param $link_url
     * @param $banner_url
     * @param $serial
     * @param $state
     * @param $admin_uid
     * @return mixed
     */
    public function editBannerInfo($id, $title, $type, $url_type, $link_url, $banner_url, $serial, $state, $admin_uid)
    {
        try{
            $UpdateArray = [
                'title' => $title,
                'type' => $type,
                'url_type' => $url_type,
                'link_url' => $link_url,
                'banner_url' => $banner_url,
                'serial' => $serial,
                'state' => $state,
                'admin_uid' => $admin_uid,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($UpdateArray);
            $return = ['code'=>20000,'msg'=>'编辑成功', 'data'=>[]];
        }catch(\Exception $e){
            DB::rollBack();
            $return = ['code'=>40000,'msg'=>'编辑失败', 'data'=>[$e->getMessage()]];
        }
        return $return;
    }

    /**
     * 修改轮播图状态
     * @param $id
     * @param $state
     * @return mixed
     */
    public function editStatus($id,  $state)
    {
        try{
            $UpdateArray = [
                'state' =>$state,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $id)
                ->update($UpdateArray);
            $return = ['code'=>20000,'msg'=>'编辑成功', 'data'=>[]];
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
