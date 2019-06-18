<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use Closure;
use Service\Models\UserBehaviorLog;

class SQLInjectionDetectionLog
{
    private static $getfilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    private static $postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    private static $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";


    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $logout = false;

        if (sql_injection_detect($request->query(), self::$getfilter)) {
            // $logout = true;
            UserBehaviorLog::insert([
                'user_id' => auth()->id() ?? 0,
                'db_user' => env('DB_USERNAME'),
                'level' => 0,
                'action' => 'SQL 注入',
                'description' => '后台 SQL GET 尝试注入' . '。路径：' . $request->path() . '，提交数据： ' . json_encode(request()->query(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
        }

        if (sql_injection_detect($request->post(), self::$postfilter)) {
            // $logout = true;
            UserBehaviorLog::insert([
                'user_id' => auth()->id() ?? 0,
                'db_user' => env('DB_USERNAME'),
                'level' => 0,
                'action' => 'SQL 注入',
                'description' => '后台 SQL POST 尝试注入' . '。路径：' . $request->path() . '，提交数据： ' . json_encode(request()->query(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ]);
        }

        if ($logout) {
            auth()->guard()->logout();
            $request->session()->invalidate();
            if ($request->ajax()) {
                return response()->json([
                    'status' => -2,
                    'code' => 500,
                    'msg' => '系统错误！',
                    'data' => []
                ])->setStatusCode(500);
            } else {
                return response()->redirectTo("/")->withErrors("系统错误！");
            }
        }

        return $next($request);
    }
}
