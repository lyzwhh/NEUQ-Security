<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/6
 * Time: 21:12
 */
namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserService
{
    public function register($userInfo)
    {
        $time = new Carbon();
        $userInfo = array_merge($userInfo,[
            'created_at' => $time,
            'updated_at' => $time
        ]);
        $userInfo['password'] = bcrypt($userInfo['password']);
        $userId = DB::table('users')->insertGetId($userInfo);
        return $userId;
    }

    public function login($id,$password)
    {
        $user = DB::table('users')->where('id',$id)->first();
        if ($user == null)
            return -1;

        if (!Hash::check($password,$user->password))
            return -2;
        else
            return $user->id;
    }

    public function getUserRole($id)
    {
        $role = DB::table('users')->where('id',$id)->value('role');
        return $role;
    }

}