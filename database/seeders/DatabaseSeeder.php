<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. SUPERADMIN (vos — sin tienda_id) ──────────────────────
        User::create([
            'name'      => 'Administrador del Sistema',
            'email'     => 'super@sistema.local',
            'password'  => Hash::make('super1234'),
            'rol'       => 'superadmin',
            'tienda_id' => null,
        ]);

        // ── 2. TIENDA INICIAL ─────────────────────────────────────────
        // El cliente cambia estos datos desde Configuración → Mi Tienda
        $tienda = Tienda::create([
            'nombre'    => 'Mi Negocio',
            'ciudad'    => 'Santa Fe',
            'direccion' => null,
            'telefono'  => null,
            'activa'    => true,
        ]);

        // ── 3. USUARIOS DE LA TIENDA ──────────────────────────────────
        User::create([
            'name'      => 'Administrador de tienda',
            'email'     => 'admin@minegocio.local',
            'password'  => Hash::make('admin1234'),
            'rol'       => 'admin',
            'tienda_id' => $tienda->id,
        ]);

        User::create([
            'name'      => 'Empleado',
            'email'     => 'empleado@minegocio.local',
            'password'  => Hash::make('empleado1234'),
            'rol'       => 'empleado',
            'tienda_id' => $tienda->id,
        ]);

        // ── 4. CLIENTE GENÉRICO (ID reservado por tienda) ─────────────
        Customer::create([
            'tienda_id'    => $tienda->id,
            'nombre'       => 'Cuenta Genérica',
            'saldo_deudor' => 0,
            'notas'        => 'Cliente genérico para fiados sin nombre. No eliminar.',
        ]);

        // ── 5. PRODUCTOS DE EJEMPLO ───────────────────────────────────
        $productos = [
            ['nombre' => 'Coca Cola 500ml',     'precio_costo' => 800,  'precio_venta' => 1200, 'stock' => 24],
            ['nombre' => 'Agua mineral 500ml',  'precio_costo' => 300,  'precio_venta' => 500,  'stock' => 30],
            ['nombre' => 'Sprite 500ml',        'precio_costo' => 800,  'precio_venta' => 1200, 'stock' => 18],
            ['nombre' => 'Cerveza Quilmes 1L',  'precio_costo' => 1800, 'precio_venta' => 2500, 'stock' => 12],
            ['nombre' => 'Alfajor Havanna',     'precio_costo' => 900,  'precio_venta' => 1400, 'stock' => 20],
            ['nombre' => 'Papas fritas 100g',   'precio_costo' => 500,  'precio_venta' => 850,  'stock' => 15],
            ['nombre' => 'Chicles Beldent',     'precio_costo' => 200,  'precio_venta' => 400,  'stock' => 40],
            ['nombre' => 'Cigarrillos Marlboro','precio_costo' => 2500, 'precio_venta' => 3200, 'stock' => 10],
            ['nombre' => 'Tomate x kg',         'precio_costo' => 600,  'precio_venta' => 900,  'stock' => 5],
            ['nombre' => 'Papa x kg',           'precio_costo' => 400,  'precio_venta' => 650,  'stock' => 8],
            ['nombre' => 'Cebolla x kg',        'precio_costo' => 350,  'precio_venta' => 600,  'stock' => 7],
            ['nombre' => 'Banana x kg',         'precio_costo' => 500,  'precio_venta' => 800,  'stock' => 6],
            ['nombre' => 'Leche La Serenísima', 'precio_costo' => 1100, 'precio_venta' => 1600, 'stock' => 20],
            ['nombre' => 'Pan lactal Bimbo',    'precio_costo' => 900,  'precio_venta' => 1400, 'stock' => 8],
            ['nombre' => 'Yogur Ser 190g',      'precio_costo' => 600,  'precio_venta' => 950,  'stock' => 3],
        ];

        foreach ($productos as $p) {
            Product::create(array_merge($p, ['tienda_id' => $tienda->id, 'activo' => true]));
        }

        // ── 6. CLIENTES DE EJEMPLO ────────────────────────────────────
        Customer::create(['tienda_id' => $tienda->id, 'nombre' => 'Juan Pérez',    'telefono' => '3424001234', 'saldo_deudor' => 3500]);
        Customer::create(['tienda_id' => $tienda->id, 'nombre' => 'María González','telefono' => '3424005678', 'saldo_deudor' => 1200]);
        Customer::create(['tienda_id' => $tienda->id, 'nombre' => 'Carlos Rodríguez', 'saldo_deudor' => 0]);
    }
}
