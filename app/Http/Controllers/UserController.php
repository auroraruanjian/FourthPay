<?php

namespace App\Http\Controllers;

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
            $user_permission = DB::table('admin_role_permissions as aup')
                //->where('aup.rule', 'LIKE', '%index')
                //->orWhere('aup.parent_id', '=', 0)
                ->orderBy('aup.id')
                ->get();
        }else{
            $user_permission = DB::table('admin_users as au')
                ->join('admin_user_has_roles as aur', 'au.id', '=', 'aur.user_id')
                ->join('admin_role_has_permission as aupr', 'aur.role_id', '=', 'aupr.role_id')
                ->join('admin_role_permissions as aup', 'aupr.permission_id', '=', 'aup.id')
                ->where('au.id', '=', auth()->id())
                // ->where(function ($query) {
                //     $query->where('aup.rule', 'LIKE', '%index')
                //         ->orWhere('aup.parent_id', '=', 0);
                // })
                ->orderBy('aup.id')
                ->get();
        }

        $permission = [];
        if( !$user_permission->isEmpty() ){
            foreach( $user_permission as $key => $_permission ){
                $_permission = (array)$_permission;
                $_permission['extra'] = json_decode($_permission['extra'],true);

                if( $_permission['parent_id'] == 0 ) {
                    $child = [];
                    if( !empty($permission[$_permission['id']]) ){
                        $child = $permission[$_permission['id']]['child'];
                    }
                    $permission[$_permission['id']]            = $_permission;
                    $permission[$_permission['id']]['child'] = $child;
                }else{
                    $permission[$_permission['parent_id']]['child'][] = $_permission;
                }
            }
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
