<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gate para middleware can:admin en las rutas de productos
        Gate::define('admin', function (User $user) {
            return $user->esAdmin();
        });
    }
}
