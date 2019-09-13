<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserGroupController extends Controller
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
            'user_group'  => [],
        ];

        $user_group = UserGroup::select([
            'user_group.id',
            'user_group.name',
            'user_group.status',
            'user_group.created_at'
        ])
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = UserGroup::count();

        if (!$user_group->isEmpty()) {
            $data['user_group'] = $user_group->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(Request $request)
    {
        $users                  = new UserGroup();
        $users->name            = $request->get('name');
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

        $user_group = UserGroup::find($id);

        if (empty($user_group)) {
            return $this->response(0, '用户组不存在');
        }

        $user_group = $user_group->toArray();

        return $this->response(1, 'success', $user_group);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $user_group = UserGroup::find($id);

        if (empty($user_group)) {
            return $this->response(0, '用户组不存在');
        }

        $user_group->name   = $request->get('name');
        $user_group->status = (int)$request->get('status',0)?true:false;

        if ($user_group->save()) {
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = $request->get('id');
        if( UserGroup::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }
}
