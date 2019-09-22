<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\CommonIndexRequest;
use App\Models\AdminRolePermissions;
use App\Models\AdminUser;
use App\Models\AdminUserHasRole;
use Illuminate\Http\Request;

class AdminController extends Controller
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

    public function getIndex(CommonIndexRequest $request)
    {
        $page  = $request->get('page', 1);
        $limit = $request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'     => 0,
            'adminlist' => [],
        ];

        $adminlist = AdminUser::select([
            'id',
            'username',
            'nickname',
            'is_locked',
            'last_ip',
            'last_time'
        ])
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = AdminUser::count();

        if (!$adminlist->isEmpty()) {
            $data['adminlist'] = $adminlist->toArray();
        }

        return $this->response(1, 'Success', $data);
    }

    public function postCreate(AdminCreateRequest $request)
    {
        $admin            = new AdminUser();
        $admin->username  = $request->get('username');
        $admin->nickname  = $request->get('nickname');
        $admin->password  = \Hash::make($request->get('password'));
        $admin->is_locked = $request->get('is_locked');

        if ($admin->save()) {
            $admin->roles()->sync($request->get('role', []));

            return $this->response(1, '添加成功');
        } else {
            return $this->response(0, '添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminUser::find($id);

        if (empty($admin)) {
            return $this->response(0, '管理员不存在失败');
        }

        $admin_role = AdminUserHasRole::select(['role_id'])->where('user_id', $id)->get();

        $admin         = $admin->toArray();
        $admin['role'] = (!$admin_role->isEmpty()) ? array_values(array_column($admin_role->toArray(), 'role_id')) : [];


        return $this->response(1, 'success', $admin);
    }

    public function putEdit(AdminCreateRequest $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminUser::find($id);

        if (empty($admin)) {
            return $this->response(0, '管理员不存在失败');
        }

        $admin->username = $request->get('username');
        $admin->nickname = $request->get('nickname');

        $password = $request->get('password');
        if (!empty($password)) {
            $admin->password = \Hash::make($password);
        }
        $admin->is_locked = $request->get('is_locked');

        if ($admin->save()) {
            $admin->roles()->sync($request->get('role', []));
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');
        if( AdminUser::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }

    public function putUnbindWechat()
    {
        $user = auth()->user();
        $user->unionid = '';

        if( $user->save() ){
            return $this->response(1,'解绑成功！');
        }else{
            return $this->response(0,'解绑失败！');
        }
    }

    public function getInfo( Request $request )
    {
        $user = auth()->user();

        if ($user->id == 1) {
            $user_permission = AdminRolePermissions::orderBy('admin_role_permissions.id','asc')->get();
        }else{
            $user_permission = AdminRolePermissions::select(['admin_role_permissions.*'])->distinct()
                ->leftJoin('admin_role_has_permission as arhp','arhp.permission_id','admin_role_permissions.id')
                ->leftJoin('admin_user_has_roles as auhr','auhr.role_id','arhp.role_id')
                ->leftJoin('admin_users as au','au.id','auhr.user_id')
                ->where('au.id',$user->id)
                ->orderBy('admin_role_permissions.id','asc')
                ->get();
        }

        $permission = [];
        if( !$user_permission->isEmpty() ){
            $permission = createPermission($user_permission->toArray());
        }

        return [
            'code'      => 1,
            'data'      => [
                'username'  => $user->username,
                'nickname'  => $user->nickname,
                'last_time' => $user->last_time,
                'last_ip'   => $user->last_ip,
                'role_name' => $user->id==1?'超级管理员':$user->roles()->name??'',
                'permission'=> $permission,
                'wechat_status' => !empty($user->unionid)?true:false,
            ]
        ];
    }
}
