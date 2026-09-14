<?php

namespace App\Providers;

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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

         // this will work as like permission checking and giving access using define value
        // Gate::define('supper_admin_super_access_login_user_activities_history', function ($user) {
        //     return  $user->id === 1 || $user->id === 11  ; // Or any complex logic for hoque and sabbir
        // });
        // // user role access and permission update action
        Gate::define('supper_admin_super_access_user_create_and_role_permission', function ($user) {
            return $user->id === 1 ; // Or any complex logic for hoque and sabbir
        });


    }


}
