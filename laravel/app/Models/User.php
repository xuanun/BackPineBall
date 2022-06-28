<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{

    protected $table = "sq_user";
    const INVALID = 0;
    const NORMAL = 1;
    protected $avatar = 'storage/avatar/avatar.jpg';

    /**
     * 获取用户信息
     * @param $account
     * @param $status
     * @return string
     */
    public function getUserInfoByAccount($account, $status)
    {
        $result = DB::table('sq_user as user')
            ->select(DB::raw('id, account, user_name, nick_name, phone, password, avatar, gander,  register_time, login_time, data_status, created_time, updated_time'))
            ->where("account", $account)
            ->where('data_status', $status)
            ->first();
        return $result ? $result : '';
    }

    /**
     * @param $user_id
     * 用户登录. 更新用户信息
     * @return mixed
     */
    public function UserLogin($user_id)
    {
        DB::beginTransaction();
        $exists = $this->existsUserById($user_id);
        if ($exists) {
            $updateArray = [
                'login_time' => date('Y-m-d H:i:s', time()),
                'updated_time' => time(),
            ];
            $user_id = DB::table($this->table)->where('id', $user_id)->update($updateArray);
            if (!$user_id) {
                DB::rollBack();
                return ['code' => 40500, 'msg' => '登录失败', 'data' => ''];
            }
        }
        DB::commit();
        return ['code' => 20000, 'msg' => '登录成功'];
    }

    /**
     * @param $user_id
     * 查询用户id存不存在
     * @return mixed
     */
    public function existsUserById($user_id)
    {
        return DB::table($this->table)
            ->where('id', $user_id)
            ->where('data_status', self::NORMAL)
            ->exists();

    }

    /**
     * @param $user_id
     * @param $password
     * 修改密码
     * @return mixed
     */
    public function editUserPassword($user_id, $password)
    {
        DB::beginTransaction();
        try {
            $UpdateArray = [
                'password' => $password,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $user_id)
                ->where('data_status', self::NORMAL)
                ->update($UpdateArray);
            $return = ['code' => 20000, 'msg' => '修改密码成功', 'data' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            $return = ['code' => 40500, 'msg' => '修改密码失败', 'data' => [$e->getMessage()]];
        }
        DB::commit();
        return $return;
    }

    /**
     * @param $user_id
     * @param $avatar
     * 修改头像
     * @return mixed
     */
    public function editUserAvatar($user_id, $avatar)
    {
        DB::beginTransaction();
        try {
            $UpdateArray = [
                'avatar' => $avatar,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $user_id)
                ->where('data_status', self::NORMAL)
                ->update($UpdateArray);
            $return = ['code' => 20000, 'msg' => '修改资料成功', 'data' => ["avatar" => env("IMAGE_URL") . $avatar]];
        } catch (\Exception $e) {
            DB::rollBack();
            $return = ['code' => 40000, 'msg' => '修改资料失败', 'data' => [$e->getMessage()]];
        }
        DB::commit();
        return $return;
    }

    /**
     * @param $user_name
     * @param $account
     * @param $phone
     * @param $page_size
     * 查看人员列表信息（链表查询）
     * @return mixed
     */
    public function getUserList($user_name, $account, $phone, $page_size)
    {
        $results = DB::table('lawyer_user as user')
            ->select(DB::raw('user.id as user_id, user_name, nick_name, real_name, account, avatar, gander, register_time, user.phone, department_id, dep.dept_name, user.data_status'))
            ->leftJoin('lawyer_department as dep', 'dep.id', '=', 'user.department_id');
        if ($account)
            $results = $results->where('account', 'like', '%' . $account . '%');
        if ($user_name)
            $results = $results->where('user_name', 'like', '%' . $user_name . '%');
        if ($phone)
            $results = $results->where('user.phone', 'like', '%' . $phone . '%');
        $results = $results
            ->orderBy('user.id', 'asc')
            ->paginate($page_size);
        return $results;
    }

    /**
     * 查询账号是否存在
     * @param $account
     * @return mixed
     */
    public function existUser($account)
    {
        return DB::table($this->table)
            ->where('account', $account)
            ->where('data_status', self::NORMAL)
            ->exists();
    }

    /**
     * @param $user_name
     * @param $account
     * @param $password
     * @param $phone
     * @param $department_id
     * @param $real_name
     * @param $user_status
     * 新增用户
     * @return mixed
     */
    public function addUser($user_name, $account, $password, $phone, $department_id, $real_name, $user_status)
    {
        try {
            $insertArray = [
                'account' => $account,
                'user_name' => $user_name,
                'nick_name' => $user_name,
                'real_name' => $real_name,
                'password' => $password,
                'avatar' => $this->avatar,
                'gander' => 1,
                'register_time' => date('Y-m-d H:i:s', time()),
                'phone' => $phone,
                'department_id' => $department_id,
                'data_status' => $user_status,
                'updated_time' => time(),
                'created_time' => time(),
            ];
            $id = DB::table($this->table)->insertGetId($insertArray);
            $return = ['code' => 20000, 'msg' => '新增成功', 'data' => ['user_id' => $id]];
        } catch (\Exception $e) {
            DB::rollBack();
            $return = ['code' => 40000, 'msg' => '新增失败', 'data' => [$e->getMessage()]];
        }
        return $return;
    }

    /**
     * @param $user_id
     * @param $password
     * @param $real_name
     * @param $user_name
     * @param $phone
     * @param $user_status
     * @param $department_id
     * 修改用户信息
     * @return mixed
     */
    public function editUser($user_id, $password, $real_name, $user_name, $phone, $department_id, $user_status)
    {
        try {
            $UpdateArray = [
                'user_name' => $user_name,
                'nick_name' => $user_name,
                'real_name' => $real_name,
                'password' => $password,
                'phone' => $phone,
                'department_id' => $department_id,
                'data_status' => $user_status,
                'updated_time' => time(),
            ];
            DB::table($this->table)
                ->where('id', $user_id)
                ->update($UpdateArray);
            $return = ['code' => 20000, 'msg' => '编辑成功', 'data' => ['user_id' => $user_id]];
        } catch (\Exception $e) {
            DB::rollBack();
            $return = ['code' => 40000, 'msg' => '编辑失败', 'data' => [$e->getMessage()]];
        }
        return $return;
    }

    /**
     * @param $user_ids
     * 批量删除用户
     * @return mixed
     */
    public function delUserIds($user_ids)
    {
        DB::beginTransaction();
        try {
            DB::table($this->table)
                ->whereIn('id', $user_ids)
                ->delete();
            $return = ['code' => 20000, 'msg' => '删除成功', 'data' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            $return = ['code' => 40000, 'msg' => '删除失败', 'data' => [$e->getMessage()]];
        }
        DB::commit();
        return $return;
    }
}
