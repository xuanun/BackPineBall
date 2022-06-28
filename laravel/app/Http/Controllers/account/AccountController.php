<?php


namespace App\Http\Controllers\account;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * 用户登录
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $input = $request->all();
        $account = isset($input['account']) ? $input['account'] : '';
        $password = isset($input['password']) ? $input['password'] : '';

//        $obj_password = encrypt($password);
//        return $obj_password;
        if(empty($account)) return response()->json(['code'=>60000,'msg'=>'参数错误, 账号不能为空', 'data'=>[]]);
        if(empty($password)) return response()->json(['code'=>60000,'msg'=>'参数错误, 密码不能为空', 'data'=>[]]);
        $token = Str::random (64);
        $redis = Redis::connection('default');
        $cacheKey = "pine_ball_user_login_".$token;
        $cacheValue = $redis->get($cacheKey);
        $model_user = new User();
        if(!empty($cacheValue)){
            $data = json_decode($cacheValue, true);
        }else{
            $object = $model_user->getUserInfoByAccount($account, 1);
            if(empty($object)) return response()->json(['code'=>60000,'msg'=>'参数错误, 账户不存在', 'data'=>[]]);
            $data =  json_decode(json_encode($object),true);
        }
        if(empty($data)) return response()->json(['code'=>40000,'msg'=>'账号不存在', 'data'=>[]]);
        $obj_password = $data['password'];
        $obj_password = decrypt($obj_password);
//        return $obj_password;
        if($password != $obj_password) return response()->json(['code'=>40000,'msg'=>'密码不正确',  'data'=>[]]);
        $return = $model_user->UserLogin($data['id']);
        if($return['code'] == 20000){
            $return['data']['user']['id'] = $data['id'];
            $return['data']['user']['token'] = $token;
            $return['data']['user']['user_name'] = $data['user_name'];
            $return['data']['user']['nick_name'] = $data['nick_name'];
            $return['data']['user']['account'] = $data['account'];
            $return['data']['user']['avatar'] = empty($data['avatar']) ? $data['avatar'] : env('IMAGE_URL').$data['avatar'];
            $return['data']['user']['gander'] = $data['gander'];
            $return['data']['user']['register_time'] = $data['register_time'];
            $return['data']['user']['login_time'] = $data['login_time'];
            $return['data']['user']['phone'] = $data['phone'];
            $return['data']['user']['created_time'] = $data['created_time'];
            $return['data']['user']['updated_time'] = $data['updated_time'];
            $return['time'] = time();
            $user_key = "pine_ball_user".$account;
            $old_token = $redis->get($user_key);
            if($old_token)
            {
                $old_cacheKey = "travel_user_login_".$old_token;
                $redis->del($old_cacheKey);
            }
            $redis->set($user_key, $token, 86400);
        }
        $redis->set($cacheKey, json_encode($data));
        return response()->json($return);
    }

    /**
     * 用户退出登录
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        $token = $request->header('token');
        if(empty($token)) return response()->json(['code'=>50000,'msg'=>'用户未登录',  'data'=>[]]);
        $redis = Redis::connection('default');
        $cacheKey = "pine_ball_user_login_".$token;
        $cacheValue = $redis->get($cacheKey);
        if(!empty($cacheValue)){
            $data = json_decode($cacheValue, true);
        }else{
            return response()->json(['code'=>50000,'msg'=>'你的登录信息已失效',  'data'=>[]]);
        }
        $account = $data['account'];
        $user_key = "pine_ball_user".$account;
        $redis->del($user_key);
        $redis->del($cacheKey);
        return response()->json(['code'=>20000,'msg'=>'退出登录成功', 'data'=>[]]);
    }

    /**
     * 用户修改密码
     * @param Request $request
     * @return mixed
     */
    public function editPassword(Request $request)
    {
        $input = $request->all();
        $token = $request->header('token');
        $redis = Redis::connection('default');
        $cacheKey = "pine_ball_user_login_".$token;
        $cacheValue = $redis->get($cacheKey);
        $model_user = new User();
        if(!empty($cacheValue)){
            $data = json_decode($cacheValue, true);
        }
        else {
            return response()->json(['code'=>40000,'msg'=>'token 已经失效', 'data'=>[]]);
        }
        $old_password = $input['old_password'] ? $input['old_password'] : '';
        $password = $input['password'] ? $input['password'] : '';
        $enterPassword = $input['password1'] ? $input['password1'] : '';
        if(empty($old_password)) return response()->json(['code'=>60000,'msg'=>'原始密码不能为空','data'=>[]]);
        if(empty($password)) return response()->json(['code'=>60000,'msg'=>'新密码不能为空', 'data'=>[]]);
        if(empty($enterPassword)) return response()->json(['code'=>60000,'msg'=>'确认密码不能为空', 'data'=>[]]);
        if($password != $enterPassword) return response()->json(['code'=>40000,'msg'=>'两次密码输入不一致', 'data'=>[]]);
        if($old_password == $password) return response()->json(['code'=>40000,'msg'=>'新密码不能与旧密码一样', 'data'=>[]]);
        if($old_password !=  decrypt($data['password']))
            return response()->json(['code'=>40000,'msg'=>'原密码不正确','data'=>[]]);

        $user_id = $data['id'];
        $e_password = encrypt($password);
        $return_data = $model_user->editUserPassword($user_id, $e_password);
        $user_key = "pine_ball_user".$data['account'];
        $redis->del($user_key);
        $redis->del($cacheKey);
        return response()->json($return_data);
    }

}
