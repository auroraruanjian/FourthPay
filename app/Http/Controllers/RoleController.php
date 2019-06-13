<?php

namespace App\Http\Controllers;

use App\Models\AdminRoles;
use Illuminate\Http\Request;

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

    //
    public function getIndex(Request $request)
    {
        $roles = AdminRoles::select(['id','name','description'])->get();

        return response()->json($roles);
    }

    public function postCreate(Request $request)
    {
        $role = new AdminRoles();
        $role->name = $request->get('name');
        $role->description = $request->get('description');

        if( $role->save ){
            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function postEdit(Request $request)
    {
        $id = $request->get('id');
        $role = AdminRoles::find( $id );
        if( empty($role) ){
            return $this->response(0,'编辑失败');
        }else{
            $role->name = $request->get('name');
            $role->description = $request->get('description');

            if( $role->save ){
                return $this->response(1,'添加成功');
            }else{
                return $this->response(0,'添加失败');
            }
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = $request->get('id');
        if( AdminRoles::find( $id )->delete() ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
