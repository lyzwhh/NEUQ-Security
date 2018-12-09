<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/6
 * Time: 21:32
 */
namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokenService
{
    public static $EXPIRE_TIME = 2;

    public function createToken($userId)
    {
        $tokenStr = md5(uniqid());
        $time = new Carbon();
        $outTime = new Carbon();
        $outTime->addHour(self::$EXPIRE_TIME);
        $data = [
            'user_id' => $userId,
            'updated_at' => $time,
            'created_at' => $time,
            'expires_at' => $outTime,
            'token' => $tokenStr,
        ];
        DB::table('tokens')->insert($data);
        return $tokenStr;
    }

    public function updateToken($userId)
    {
        $time = new Carbon();
        $outTime = new Carbon();
        $outTime->addHour(self::$EXPIRE_TIME);
        $tokenStr = md5(uniqid());
        $data = [
            'token' => $tokenStr,
            'updated_at' => $time,
            'expires_at' => $outTime
        ];

        DB::table('tokens')->where('user_id', $userId)->update($data);
        return $tokenStr;
    }

    public function makeToken($userId)
    {
        $tokenStr = DB::table('tokens')->where('user_id', $userId)->first();
        if ($tokenStr == null)
        {
            $tokenStr = $this->createToken($userId);
        }
        else
        {
            $tokenStr = $this->updateToken($userId);
        }
        return $tokenStr;
    }


}