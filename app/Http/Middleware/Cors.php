<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof BinaryFileResponse) {
            return $response;
        }
        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept,token,tokenId,token_type,Accept,X-Requested-With');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH,DELETE,PUT, OPTIONS');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
