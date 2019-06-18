<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param int    $code      0:失败   1:成功  <-1:异常编码
     * @param string $msg       提示消息
     * @param array  $data      响应数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(int $code ,string $msg = '',array $data = [] )
    {
        return response()->json([
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data,
        ]);
    }
}
