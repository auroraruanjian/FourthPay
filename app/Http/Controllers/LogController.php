<?php

namespace App\Http\Controllers;

use App\Models\AdminRequestLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getRequestLog(Request $request)
    {
        $page   = $request->get('page',1);
        $limit  = $request->get('limit');

        $start  = ($page-1) * $limit;

        $data   = [
            'total'     => 0,
            'log'       => [],
        ];

        $admin_request_log = AdminRequestLog::orderBy('id','asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = AdminRequestLog::count();

        if( !$admin_request_log->isEmpty() ){
            $data['log'] = $admin_request_log->toArray();

            return $this->response(1,'Success',$data);
        }

        return $this->response(1,'',$data);
    }

    public function getRequestLogDetail(Requeset $requeset)
    {

    }


}
