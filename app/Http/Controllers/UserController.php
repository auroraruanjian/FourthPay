<?php

namespace App\Http\Controllers;

use App\Models\AdminRolePermissions;
use Illuminate\Http\Request;
use DB;

class UserController extends Controller
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

    public function getInfo( Request $request )
    {

        if (auth()->id() == 1) {
            $user_permission = AdminRolePermissions::orderBy('admin_role_permissions.id','asc')->get();
        }else{
            $user_permission = AdminRolePermissions::select(['admin_role_permissions.*'])->distinct()
                ->leftJoin('admin_role_has_permission as arhp','arhp.permission_id','admin_role_permissions.id')
                ->leftJoin('admin_user_has_roles as auhr','auhr.role_id','arhp.role_id')
                ->leftJoin('admin_users as au','au.id','auhr.user_id')
                ->where('au.id',auth()->id())
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
                'username'  => auth()->user()->username,
                'usernick'  => auth()->user()->nickname,
                'permission'=> $permission,
            ]
        ];
    }

}
