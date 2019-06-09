<?php

namespace App\Http\Middleware;

use Closure;
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
        $this->authenticate($request, $guards);

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
