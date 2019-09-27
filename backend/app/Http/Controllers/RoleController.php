<?php

namespace App\Http\Controllers;

use App\Models\AdminRoleHasPermission;
use App\Models\AdminRoles;
use Illuminate\Http\Request;
use DB;

class RoleController extends Controller
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

    /**
     * 获取所有角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex(Request $request)
    {
        $roles = AdminRoles::select(['id','name','description'])->get();

        $user_permission = DB::table('admin_role_permissions as aup')
            ->orderBy('aup.id')
            ->get();

        $data = [
            'roles'         => !$roles->isEmpty()?$roles->toArray():[],
            'permission'    => createPermission($user_permission),
        ];

        return $this->response(1,'success',$data);
    }

    /**
     * 创建角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCreate(Request $request)
    {
        $role = new AdminRoles();
        $role->name = $request->get('name');
        $role->description = $request->get('description');

        if( $role->save() ){
            $role->permissions()->sync($request->get('routes', []));

            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    /**
     * 获取单个角色信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $role = AdminRoles::find($id);

        $permission = AdminRoleHasPermission::select(['permission_id'])->where('role_id',$id)->get();

        if( !empty($role) ){
            $data = $role->toArray();
            $permission = $permission->isEmpty()?[]:array_values(array_column($permission->toArray(),'permission_id'));
            $data['permission'] = $permission;

            return $this->response(1,'success',$data);
        }
        return $this->response(0,'对不起，角色不存在');
    }

    /**
     * 编辑角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');
        $role = AdminRoles::find( $id );
        if( empty($role) ){
            return $this->response(0,'编辑失败');
        }else{
            $role->name = $request->get('name');
            $role->description = $request->get('description');

            if( $role->save() ){
                $role->permissions()->sync($request->get('routes', []));

                return $this->response(1,'修改成功');
            }else{
                return $this->response(0,'修改失败');
            }
        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');
        if( AdminRoles::find( $id )->delete() ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
