<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 测试API
     */
    public function getIndex(Request $request)
    {
        return $this->response(1,'1111');
    }
}
