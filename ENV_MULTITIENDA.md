# Variables .env — Fork Multi-tienda

Agregar estas líneas al `.env` del proyecto:

```env
# ── Multi-tienda ─────────────────────────────────────────────────────
#
# false (default): vos creás las tiendas via seeder o tinker
#                  el cliente NO puede crear ni eliminar tiendas
#
# true:            el cliente superadmin puede crear/eliminar tiendas
#                  (cobrar este upgrade al cliente)
#
ALLOW_TIENDA_MANAGEMENT=false
```

## Credenciales iniciales (seeder)

| Rol        | Email                     | Contraseña   | Tienda         |
|------------|---------------------------|--------------|----------------|
| superadmin | super@sistema.local       | super1234    | (todas)        |
| admin      | admin@minegocio.local     | admin1234    | Mi Negocio     |
| empleado   | empleado@minegocio.local  | empleado1234 | Mi Negocio     |

> ⚠️ Cambiar todas las contraseñas antes de entregar al cliente.

## Crear una segunda tienda (sin habilitar el flag)

```bash
php artisan tinker

$t = App\Models\Tienda::create([
    'nombre'    => 'Sucursal Norte',
    'ciudad'    => 'Santa Fe',
    'direccion' => 'Bv. Gálvez 2345',
]);

App\Models\User::create([
    'name'      => 'Admin Sucursal',
    'email'     => 'admin2@minegocio.local',
    'password'  => bcrypt('password'),
    'rol'       => 'admin',
    'tienda_id' => $t->id,
]);

// Cliente genérico de la nueva tienda
App\Models\Customer::create([
    'tienda_id' => $t->id,
    'nombre'    => 'Cuenta Genérica',
]);
```

## Habilitar gestión de tiendas (upgrade de pago)

Simplemente cambiar en `.env`:
```env
ALLOW_TIENDA_MANAGEMENT=true
```

Y limpiar caché:
```bash
php artisan config:clear
```

Eso es todo. Sin tocar código, sin nuevo deploy.
