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
    public static $EXPIRE_TIME = 360;  //  若是 则 hours => 15days

    public static $DEPARTMENT_CODE = array(
        101    =>  "安全工作处",          102    =>  "财经处",
        103    =>  "党委宣传部",          104    =>  "党委组织部",
        105    =>  "东秦工会委员会",      106    =>  "管理学院",
        107    =>  "共青团东秦委员会",    108    =>  "后勤管理处",
        109    =>  "国际合作与交流处",    110    =>  "经济学院",
        111    =>  "基建管理处",          112    =>  "纪律检查委员会办公室",
        113    =>  "计算机与通信工程学院",114    =>  "教务处",
        115    =>  "科技处",              116    =>  "控制工程学院",
        117    =>  "人事处",              118    =>  "社会科学研究院",
        119    =>  "数学与统计学院",      120    =>  "离退休工作处",
        121    =>  "体育部",              122    =>  "学生工作处",
        123    =>  "学校办公室",          124    =>  "信息化建设与管理办公室",
        125    =>  "外国语言文化学院",    126    =>  "研究生分院",
        127    =>  "资源与材料学院",      128    =>  "资产与实验室管理处",
        129    =>  "招生就业处"
);

    public function createToken($userId)
    {
        $tokenStr = md5(uniqid());
        $time = new Carbon();
        $outTime = new Carbon();
        $outTime->addYear(self::$EXPIRE_TIME);
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
        $outTime->addYear(self::$EXPIRE_TIME);
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
    public function verifyToken($tokenStr)
    {
        $res = $this->getToken($tokenStr);
        if($res == null)
            return -1;
        else{
            $time = new Carbon();
            if ($res->expires_at > $time) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    public function getToken($tokenStr)
    {
        return DB::table('tokens')->where('token',$tokenStr)->first();
    }

    public function getUserByToken($tokenStr)
    {
        $tokenInfo = $this->getToken($tokenStr);
        $userInfo = DB::table('users')->where('id',$tokenInfo->user_id)->select('id','name','role')->first();
        if ($userInfo->role > 100)        // 当tokenMiddleware 调用该函数时 学院账号插入部门信息
        {
            if (array_key_exists($userInfo->role,self::$DEPARTMENT_CODE))
            {
                $userInfo->department = self::$DEPARTMENT_CODE[$userInfo->role];
            }
            else
            {
                $userInfo->department = 404;
            }
        }
        return $userInfo;
    }

}