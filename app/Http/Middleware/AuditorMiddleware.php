<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserService;

class AuditorMiddleware
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if ($request->user == null)   //for login
        {
            $role = $this->userService->getUserRole($request->id);

            if ($role > 100)     // attention 1
            {
                return $next($request);
            }
            else
            {
                return response()->json([
                    'code'  =>  '304',
                    'message'   =>  '该账号无权，请确认该账号是否为web端账户'
                ]);
            }
        }
        else   //for operation
        {
            if ($request->user->role > 100)  //attention 2
            {
                return $next($request);
            }
            else
            {
                return response()->json([
                    'code'  =>  '304',
                    'message'   =>  '该账号无权'
                ]);
            }
        }

    }
}
