<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Gate;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try{
            $this->authenticate($request, $guards);
        }catch( AuthenticationException $e ){
            if( $request->ajax() ){
                return response()->json([
                    'code'  => -401,
                    'msg'   => __('auth.login.false'),
                ])->setStatusCode(401);
            }else{
                abort(401,__('auth.login.false'));
            }
        }

        $rule = $request->path();

        if( !in_array($rule,[
            '/user/info'
            ]) ||
        Gate::check($rule) ){
            return $next($request);
        }

        if( $request->ajax() ){
            return response()->json([
                'code'  => -403,
                'msg'   => __('auth.permissions.false'),
            ]);
        }else{
            abort(403,__('auth.permissions.false'));
        }
    }
}
