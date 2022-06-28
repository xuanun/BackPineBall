<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    protected $table = "sq_message";
    /**
     * 查询留言列表
     * @param $page_size
     * @param $user_name
     * @param $phone
     * @return mixed
     */
    public function getList($page_size, $user_name, $phone)
    {
        $results = DB::table($this->table)
            ->select(DB::raw('id, user_name, phone, content, remarks, status, created_time, updated_time'));
        if($user_name){
            $results = $results->where('user_name','like','%'.$user_name.'%');
        }
        if($phone){
            $results = $results->where('phone','like','%'.$phone.'%');
        }
        $results = $results
            ->orderBy('updated_time', 'desc')
            ->orderBy('status', 'asc')
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
     * @param $user_name
     * @param $phone
     * @param $content
     * 新增留言
     * @return mixed
     */
    public function addMessage($user_name, $phone, $content)
    {
        DB::beginTransaction();
        try{
            $insertArray = [
                'user_name' => $user_name,
                'phone' => $phone,
                'content' => $content,
                'remarks' => '',
                'status' => 0,
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
     * 处理留言
     * @param $id
     * @param $remarks
     * @param $status
     * @return mixed
     */
    public function editMessage($id,  $remarks, $status)
    {
        try{
            $UpdateArray = [
                'remarks' => $remarks,
                'status' => $status,
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
}
