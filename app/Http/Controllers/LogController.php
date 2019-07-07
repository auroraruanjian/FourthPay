<?php

namespace App\Http\Controllers;

use App\Models\AdminLoginLog;
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

    public function getLoginLog(Request $request)
    {
        $page   = $request->get('page',1);
        $limit  = $request->get('limit');

        $start  = ($page-1) * $limit;

        $data   = [
            'total'     => 0,
            'log'       => [],
        ];

        $admin_request_log = AdminLoginLog::select([
                'admin_login_log.id',
                'admin_login_log.user_id',
                'admin_users.username',
                'admin_login_log.domain',
                'admin_login_log.province',
                'admin_login_log.browser',
                'admin_login_log.browser_version',
                'admin_login_log.os',
                'admin_login_log.device',
                'admin_login_log.ip',
                'admin_login_log.created_at',
            ])
            ->leftJoin('admin_users','admin_users.id','admin_login_log.user_id')
            ->orderBy('admin_login_log.id','asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = AdminLoginLog::count();

        if( !$admin_request_log->isEmpty() ){
            $data['log'] = $admin_request_log->toArray();

            return $this->response(1,'Success',$data);
        }

        return $this->response(1,'',$data);
    }
}
