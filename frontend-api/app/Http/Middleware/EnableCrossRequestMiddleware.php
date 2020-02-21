<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
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
        // 用于跨域调用
        if($request->isMethod('OPTIONS')){
            $response = response('',200);
        }else{
            $response = $next($request);
        }

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allow_origin = [
            'http://localhost:8080',
            'http://www.laravel-admin.me',
        ];
        if (in_array($origin, $allow_origin)) {
            $response->headers->add([
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Access-Token, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN, x-requested-with',
                'Access-Control-Expose-Headers' => 'Authorization, authenticated',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
                'Access-Control-Allow-Credentials' => 'true'
            ]);

        }
        return $response;
    }
}
