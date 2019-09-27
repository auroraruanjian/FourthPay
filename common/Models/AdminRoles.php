<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoles extends Model
{
    //
    protected $table='admin_roles';

    public $timestamps = false;

    public function permissions()
    {
        return $this->belongsToMany(
            AdminRolePermissions::class,
            'admin_role_has_permission',
            'role_id',
            'permission_id'
        );
    }
    public function users()
    {
        return $this->belongsToMany(
            AdminUser::class,
            'admin_user_has_role',
            'role_id',
            'user_id'
        );
    }

    // 给角色添加权限
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save($permission);
    }
}
