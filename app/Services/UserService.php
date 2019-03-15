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

    public function getNormalScannerList()
    {
        $list = DB::table('users')->where('role',1)->select('id')->get();     // 只能查看 '普通' 安保人员的账号
        return $list;
    }
    public function updateUserInfo($userId, $userInfo)
    {
        $time = new Carbon();

        $userInfo = array_merge($userInfo, [
            'updated_at' => $time
        ]);
        DB::table('users')->where('id', $userId)->update($userInfo);
        return true;
    }

    public function resetNormalScannerPassword($resetInfo)
    {
        $userInfo['password'] = bcrypt($resetInfo['password']);
        self::updateUserInfo($resetInfo['id'],$userInfo);
    }

}