<?php

namespace App\Http\Controllers;

use App\Models\Notices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NoticesController extends Controller
{
    private $filed = [
        'subject'       => '',
        'published_at'  => '',
        'sort'          => 0,
        'is_show'       => false,
        'is_top'        => false,
        'is_alert'      => false,
        'content'       => '',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function postIndex(Request $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'     => 0,
            'adminlist' => [],
        ];

        $notice_list = Notices::select([
                'notices.id',
                'notices.subject',
                'notices.created_admin_user_id',
                'notices.verified_admin_user_id',
                'notices.sort',
                'notices.is_show',
                'notices.is_top',
                'notices.is_alert',
                'notices.created_at',
                'notices.published_at',
                'notices.verified_at',
                'admin_users.username as created_admin_username',
            ])
            ->leftJoin('admin_users','admin_users.id','notices.created_admin_user_id')
            ->orderBy('notices.sort','asc')
            ->orderBy('notices.id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Notices::count();

        if (!$notice_list->isEmpty()) {
            $data['noticelist'] = $notice_list->toArray();
        }

        return $this->response(1, 'Success', $data);
    }

    public function postCreate(Request $request)
    {
        $notice = new Notices();
        foreach( $this->filed as $filed => $default_val ){
            $notice->$filed = $request->get($filed,$default_val);
        }

        $notice->created_admin_user_id = auth()->id();
        $notice->created_at = (string)Carbon::now();

        if( $notice->save() ){
            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');
        if( empty($id) ){
            return $this->response(0,'数据不存在!');
        }

        $filed = array_merge(array_keys($this->filed),['id']);

        $notices = Notices::select($filed)
            ->where('id','=',$id)
            ->first();

        if( !empty($notices) ){
            return $this->response(1,'Success',$notices->toArray());
        }else{
            return $this->response(0,'数据不存在');
        }
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');
        if( empty($id) ){
            return $this->response(0,'数据不存在!');
        }

        $notice = Notices::find($id);
        foreach( $this->filed as $filed => $default_val ){
            $notice->$filed = $request->get($filed,$default_val);
        }
        $notice->created_admin_user_id = auth()->id();
        $notice->created_at = (string)Carbon::now();

        if( $notice->save() ){
            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');
        if( empty($id) ){
            return $this->response(0,'数据不存在!');
        }

        // TODO:此处需要加权限验证
        if( Notices::delete($id) ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
