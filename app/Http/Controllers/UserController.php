<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\TokenService;

class UserController extends Controller
{
    private $userService;
    private $tokenService;
    public function __construct(UserService $userService,
                                TokenService $tokenService)
    {
        $this->userService=$userService;
        $this->tokenService=$tokenService;
    }

    private $rule = [
        'id'      =>      'required',
        'password'  =>      'required|max:32'
    ];

    public function register(Request $request)
    {
        $this->validate($request,[
            'password'  =>  $this->rule['password'],
        ]);
        $userInfo = $request->all();
        $userId = $this->userService->register($userInfo);
        $tokenStr = $this->tokenService->makeToken($userId);
        return response()->json([
                'code'      =>  0,
                'message'   =>  '注册成功',
                'data'      =>  [
                    'tokenStr'  =>  $tokenStr,
                    'user_id'   =>  $userId,
                ]
            ]);

    }

    public function login(Request $request)
    {
        $this->validate($request,[
            'id'      =>$this->rule['id'],
            'password'  =>$this->rule['password'],
        ]);
        $loginInfo = $request->all();
        $userId = $this->userService->login($loginInfo['id'],$loginInfo['password']);
        if ($userId == -1)
        {
            return response()->json([
                'code'      =>  302,
                'message'   =>  '用户不存在',
            ]);
        }
        else if ($userId == -2)
        {
            return response()->json([
                'code'      =>  303,
                'message'   =>  '密码错误',
            ]);
        }
        else
        {
            $tokenStr = $this->tokenService->makeToken($userId);
            return response()->json([
                'code'      =>  0,
                'message'   =>  '登陆成功',
                'data'      =>  [
                    'user_id'   =>  $userId,
                    'tokenStr'  =>  $tokenStr,
                ]
            ]);
        }
    }

}
