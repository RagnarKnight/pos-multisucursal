<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Gates de rol ──────────────────────────────────────────────
        Gate::define('admin',      fn($u) => in_array($u->rol, ['admin', 'superadmin']));
        Gate::define('superadmin', fn($u) => $u->rol === 'superadmin');

        // Gestión de tiendas: superadmin + flag en .env habilitado
        // ALLOW_TIENDA_MANAGEMENT=true → el cliente puede crear/eliminar tiendas
        // ALLOW_TIENDA_MANAGEMENT=false (default) → solo vos creás tiendas via seeder/tinker
        Gate::define('gestionar-tiendas', fn($u) =>
            $u->rol === 'superadmin' && config('variables.multi_tienda', false)
        );

        // Editar configuración de UNA tienda: admin de esa tienda o superadmin
        Gate::define('configurar-tienda', fn($u) =>
            in_array($u->rol, ['admin', 'superadmin'])
        );
    }
}
