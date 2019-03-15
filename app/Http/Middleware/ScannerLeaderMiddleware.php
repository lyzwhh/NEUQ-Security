<?php

namespace App\Http\Middleware;

use Closure;

class ScannerLeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)    //一定要在 tokenMiddleware 后面
    {
        $role = $request->user->role;
        if ($role == 66)
        {
            return $next($request);

        }
        else
        {
            return response([
                'code'  =>304,
                'message'   =>  '此账号不是安保处总负责人账号，无权'
            ]);

        }
    }
}
