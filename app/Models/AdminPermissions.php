<?php

namespace App\Models;

use App\Models\AdminRoles;
use Illuminate\Database\Eloquent\Model;

class AdminPermissions extends Model
{
    //
    protected $table='admin_permissions';

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
