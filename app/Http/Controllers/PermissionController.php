<?php

namespace App\Http\Controllers;

use App\Models\AdminRolePermissions;
use Illuminate\Http\Request;

class PermissionController extends Controller
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

    public function getIndex(Request $request)
    {
        $parent_id = $request->get('id',0);
        $adminlist = AdminRolePermissions::select(['id','rule','name'])->where('parent_id',$parent_id)->orderBy('id','asc')->get();
        if( !$adminlist->isEmpty() ){
            $adminlist = $adminlist->toArray();
        }else{
            $adminlist = [];
        }
        return $this->response(1,'Success',$adminlist);
    }

    public function postCreate(Request $request)
    {
        $admin = new AdminRolePermissions();

        if( $admin->save() ){

            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminRolePermissions::find($id);

        if( empty($admin) ){
            return $this->response(0,'管理员不存在失败');
        }

        return $this->response(1,'success',$admin);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminRolePermissions::find($id);

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
            return $this->response(1,'编辑成功');
        }else{
            return $this->response(0,'编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');

        $admin = AdminRolePermissions::find($id);

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
