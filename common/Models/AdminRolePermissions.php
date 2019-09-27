<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRolePermissions extends Model
{
    //
    protected $table='admin_role_permissions';

    public function roles()
    {
        return $this->belongsToMany(
            AdminRoles::class,
            'admin_role_has_permission',
            'permission_id',
            'role_id'
        );
    }
}
