<?php

$request = request();

// 优先尝试匹配已经配置的静态路由
try {
    Route::getRoutes()->match($request);
    return; // 匹配成功，则返回
} catch (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    // 匹配失败，继续下面的代码
}

// 上面匹配失败了，现在来尝试动态配置路由
$pathinfo = strtolower(preg_replace('#/+#', '/', $request->path())); // delete unnecessary slashes
if ($pathinfo == '/' || $pathinfo[0] == '_') {
    return;  // 下划线前缀开头的跳过
}
$pathinfo_arr = explode('/', $pathinfo);
$count_pathinfo_arr = count($pathinfo_arr);

// 对于一级和二级目录访问，我们使用动态路由，二级以上的，需要配置为静态路由。
if ($count_pathinfo_arr == 1 || $count_pathinfo_arr == 2) {
    try {
        $namespace = (new App\Providers\RouteServiceProvider(null))->getNamespace();

        // 处理带下划线控制器自动转换为驼峰规则
        $fix_path = array_map(function( $val ){
            return ucfirst($val);
        },explode('_',$pathinfo_arr[0]));

        $controller = implode('',$fix_path) . 'Controller';

        // 看看这个控制器是否存在
        $controller_reflection= new ReflectionClass($namespace . '\\' . $controller);
        $method = strtolower($request->method());
        switch ($method) { // 检查请求类型
            case 'get':    // 如果是 GET 请求
            case 'post':   // 如果是 POST 请求
            case 'put':    // 如果是 PUT 请求
            case 'delete': // 如果是删除请求
                // 默认 action 为 {$method}Index
                $action = empty($pathinfo_arr[1]) ? "{$method}Index" : "$method" . ucfirst($pathinfo_arr[1]);

                // 看看控制器方法是否存在
                $controller_reflection->getMethod($action);

                // 添加动态路由
                Route::$method($request->path(), "{$controller}@{$action}");

                break;
            default:
                break;
        }
    } catch (ReflectionException $e) { // 找不到控制器或者方法
        abort_unless(config('app')['debug'], 404, '页面不存在！');
        throw $e;
    }
}
