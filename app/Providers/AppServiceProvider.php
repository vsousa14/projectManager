<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends \Illuminate\Foundation\Support\Providers\AuthServiceProvider 
{

     protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Paginator::useBootstrapFive();
        Gate::define('view-backoffice', fn($user) => $user->hasPermissionTo('View Backoffice'));
        Gate::define('manage-users', fn($user) => $user->hasPermissionTo('View Users') || $user->hasPermissionTo('Create User') || $user->hasPermissionTo('Update User') || $user->hasPermissionTo('Delete User'));
        Gate::define('manage-roles', fn($user) => $user->hasPermissionTo('View Roles') || $user->hasPermissionTo('Create Role') || $user->hasPermissionTo('Update Role') || $user->hasPermissionTo('Delete Role'));
    }
}
