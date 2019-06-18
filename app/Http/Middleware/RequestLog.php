<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use Closure;
use App\Models\AdminRequestLog;

class RequestLog
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request_data = $request->all();
        if (!auth()->user()                   // 未登录
            || empty($request_data)           // 未提交数据
        ) {
            // 不记录日志
            return $next($request);
        }

        $log = new AdminRequestLog;

        // if (app()->isLocal()) {
        //    $log->setConnection(null);        // 本地环境使用默认的数据库连接
        // }

        //参数过滤
        $path = strtolower($request->path());
        $request_data = $this->requestFilter($path, $request_data);

        $log->username = auth()->user()->username;
        $log->path     = $request->path();
        $log->request  = json_encode($request_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        register_shutdown_function(function ($log) {
            $log->save();          // 结束请求后再保存日志
        }, $log);

        return $next($request);
    }

    /**
     * 参数过滤
     * @param string $path          请求路径
     * @param array  $request_data  请求参数
     * @return mixed                过滤后的参数
     */
    private function requestFilter($path, $request_data)
    {
        switch ($path) {
            default:
                $filter_keys = [];
        }

        if (!empty($filter_keys)) {
            foreach ($filter_keys as $key) {
                unset($request_data[$key]);
            }
        }
        
        return $request_data;
    }
}
