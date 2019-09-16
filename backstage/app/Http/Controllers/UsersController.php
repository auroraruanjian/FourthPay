<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;

class UsersController extends Controller
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

    public function postIndex(Request $request)
    {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'       => 0,
            'users_list' => [],
        ];

        $userslist = Users::select([
            'users.id',
            'clients.account',
            'users.username',
            'user_group.name as user_group_name',
            'users.nickname',
            'users.status',
            'users.last_ip',
            'users.last_time',
            'users.created_at'
        ])
            ->leftJoin('clients','clients.id','users.client_id')
            ->leftJoin('user_group','user_group.id','users.user_group_id')
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Users::count();

        if (!$userslist->isEmpty()) {
            $data['users_list'] = $userslist->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(Request $request)
    {
        $users                  = new Users();
        $users->client_id       = $request->get('client_id');
        $users->username        = $request->get('username','');
        $users->nickname        = $request->get('nickname','');
        $users->password        = $request->get('password','');
        $users->user_group_id   = $request->get('user_group_id');
        $users->status          = (int)$request->get('status',0)?true:false;

        if( $users->save() ){
            return $this->response(1, '添加成功');
        } else {
            return $this->response(0, '添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $users = Users::find($id);

        if (empty($users)) {
            return $this->response(0, '配置不存在');
        }

        $users = $users->toArray();

        return $this->response(1, 'success', $users);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $users = Users::find($id);

        if (empty($users)) {
            return $this->response(0, '配置不存在失败');
        }

        $users->client_id       = $request->get('client_id');
        $users->username        = $request->get('username','');
        $users->nickname        = $request->get('nickname','');
        $users->password        = $request->get('password','');
        $users->user_group_id   = $request->get('user_group_id');
        $users->status          = (int)$request->get('status',0)?true:false;

        if ($users->save()) {
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = $request->get('id');
        if( Users::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }
}
