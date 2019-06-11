<?php

namespace App\Providers;

use App\Models\AdminRolePermissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        Gate::before(function ($user, $ability){
            if ($user->id == 1 ) {
                //return true;
            }

            $permission = AdminRolePermissions::where('rule', '=', $ability)->first();

            if ($permission && !Gate::has($ability)) {
                // 对访问权限定义 Gate
                Gate::define($ability, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        });

        $this->registerPolicies();

        //
    }
}
