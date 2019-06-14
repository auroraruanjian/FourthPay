<?php

namespace App\Http\Controllers;

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

    public function getIndex()
    {
        $adminlist = AdminUser::select(['id','username','nickname','is_locked','last_ip','last_time'])->orderBy('id','asc')->get();
        if( !$adminlist->isEmpty() ){
            return $this->response(1,'Success',$adminlist->toArray());
        }else{
            return $this->response(0,'管理员为空!');
        }
    }

    public function postCreate(Request $request)
    {
        $admin = new AdminUser();
        $admin->username = $request->get('username');
        $admin->nickname = $request->get('nickname');
        $admin->password = \Hash::make($request->get('password'));
        $admin->is_locked = $request->get('is_locked');

        if( $admin->save() ){
            $admin->roles()->sync($request->get('role',[]));

            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminUser::find($id);

        if( empty($admin) ){
            return $this->response(0,'管理员不存在失败');
        }

        $admin_role = AdminUserHasRole::select(['role_id'])->where('user_id',$id)->get();

        $admin = $admin->toArray();
        $admin['role'] = (!$admin_role->isEmpty())?array_values(array_column($admin_role->toArray(),'role_id')):[];


        return $this->response(1,'success',$admin);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminUser::find($id);

        if( empty($admin) ){
            return $this->response(0,'管理员不存在失败');
        }

        $admin->username = $request->get('username');
        $admin->nickname = $request->get('nickname');

        $password = $request->get('password');
        if( !empty($password) ){
            $admin->password = \Hash::make($password);
        }
        $admin->is_locked = $request->get('is_locked');

        if( $admin->save() ) {
            $admin->roles()->sync($request->get('role', []));
            return $this->response(1,'编辑成功');
        }else{
            return $this->response(0,'编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminUser::find($id);

        if( empty($admin) ){
            return $this->response(0,'管理员不存在失败');
        }

        if( $admin->delete() ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
