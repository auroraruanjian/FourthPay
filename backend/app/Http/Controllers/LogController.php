<?php

namespace App\Http\Controllers;

use Common\Models\AdminLoginLog;
use Common\Models\AdminRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function getSystemLog(Request $request)
    {
        $path = $request->get('path');

        $all = [];

        $dir_tree = !empty($path)?explode('/',$path):[];

        $tmp = '';
        $tree = [];
        foreach( $dir_tree as $key => $_tree ){
            if( $key > 0 ) {
                $tmp = $tmp.'/'.$_tree;
            }else{
                $tmp = $_tree;
            }

            $tree[] = [
                'path'  => $tmp,
                'name'  => $_tree
            ];
        }

        $dir  = Storage::disk('logs')->directories($path);
        foreach( $dir as $key => $_dir){
            $all[] = [
                'type'          => 'dir',
                'name'          => $_dir,
                'size'          => 0,
                'lastModified'  => 0,
            ];
        }

        $files  = Storage::disk('logs')->files($path);
        foreach( $files as $key => $filename ){
            // 不显示以[.]开头的隐藏文件
            if( strpos($filename,'.') === 0 ) continue;

            $all[] = [
                'type'          => 'file',
                'name'          => $filename,
                'size'          => round(Storage::disk('logs')->size($filename)/1024/1024,4),
                'lastModified'  => date('Y-m-d H:i:s',Storage::disk('logs')->lastModified($filename)),
            ];
        }

        return $this->response(1,'Success',[
            'tree'  => $tree,
            'list'  => $all,
        ]);
    }

    public function getSystemLogFile(Request $request)
    {
        $flag       = $request->get('flag');
        $filename   = $request->get('filename');

        $fileHandle = Storage::disk('logs');

        // 检查文件是否存在
        if( empty($filename) || !$fileHandle->exists($filename) ){
            abort(404,'文件不存在！');
        }

        // 如果是查看并且文件大小低于5M则显示
        if( $flag == 'view' && $fileHandle->size($filename) <= 5*1024*1024 ){
            return response()->file(config('filesystems.disks.logs.root').'/'.$filename);
        }

        return response()->download(config('filesystems.disks.logs.root').'/'.$filename);
    }
}
