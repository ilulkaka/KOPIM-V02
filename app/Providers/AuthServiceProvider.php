<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('menu-trx', function ($user) {
            return in_array($user->role, ['Administrator', 'Kasir', 'Pengurus']);
        });

        Gate::define('menu-b2b', function ($user) {
            return in_array($user->role, ['Administrator']);
        });

        Gate::define('menu-anggota', function ($user) {
            return in_array($user->role, ['Administrator', 'Pengurus']);
        });
    }
}
