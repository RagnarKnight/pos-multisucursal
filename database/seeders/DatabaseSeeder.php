<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ──────────────────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@santafe.local',
            'password' => bcrypt('admin1234'),
            'rol'      => 'admin',
        ]);

        User::create([
            'name'     => 'Empleado',
            'email'    => 'empleado@santafe.local',
            'password' => bcrypt('empleado1234'),
            'rol'      => 'empleado',
        ]);

        // ── Cliente genérico para fiados sin identificar ───────────
        // ID=1 reservado — el POS lo usa automáticamente
        Customer::create([
            'nombre'       => 'Cuenta Genérica',
            'telefono'     => null,
            'saldo_deudor' => 0,
            'notas'        => 'Cliente por defecto para ventas fiadas sin cliente identificado. Editá la orden luego para asignar el cliente correcto.',
        ]);

        // ── Productos típicos de kiosco/verdulería ─────────────────
        $productos = [
            ['nombre' => 'Coca Cola 2.25L',     'precio_costo' => 1200, 'precio_venta' => 1800, 'stock' => 24],
            ['nombre' => 'Pepsi 2.25L',          'precio_costo' => 1100, 'precio_venta' => 1700, 'stock' => 12],
            ['nombre' => 'Agua Mineral 500ml',   'precio_costo' =>  350, 'precio_venta' =>  600, 'stock' => 48],
            ['nombre' => 'Yerba Mate 1kg',       'precio_costo' => 2500, 'precio_venta' => 3500, 'stock' => 10],
            ['nombre' => 'Alfajor Havanna',      'precio_costo' =>  800, 'precio_venta' => 1200, 'stock' => 30],
            ['nombre' => 'Alfajor Milka',        'precio_costo' =>  600, 'precio_venta' =>  900, 'stock' => 30],
            ['nombre' => 'Cigarrillos Marlboro', 'precio_costo' => 1800, 'precio_venta' => 2200, 'stock' => 20],
            ['nombre' => 'Pan Lactal Bimbo',     'precio_costo' =>  900, 'precio_venta' => 1300, 'stock' =>  8],
            ['nombre' => 'Leche Entera 1L',      'precio_costo' =>  700, 'precio_venta' => 1000, 'stock' => 15],
            ['nombre' => 'Tomate x Kg',          'precio_costo' =>  400, 'precio_venta' =>  700, 'stock' => 10],
            ['nombre' => 'Papa x Kg',            'precio_costo' =>  350, 'precio_venta' =>  600, 'stock' => 20],
            ['nombre' => 'Cebolla x Kg',         'precio_costo' =>  300, 'precio_venta' =>  500, 'stock' => 15],
            ['nombre' => 'Fideos Tallarín 500g', 'precio_costo' =>  600, 'precio_venta' =>  950, 'stock' => 25],
            ['nombre' => 'Aceite Girasol 900ml', 'precio_costo' => 1600, 'precio_venta' => 2200, 'stock' => 10],
            ['nombre' => 'Azúcar 1kg',           'precio_costo' =>  700, 'precio_venta' => 1100, 'stock' => 12],
        ];

        foreach ($productos as $p) {
            Product::create(array_merge($p, ['activo' => true]));
        }

        // ── Clientes de ejemplo (además de la Cuenta Genérica) ─────
        $clientes = [
            ['nombre' => 'María González',  'telefono' => '3425-123456', 'saldo_deudor' => 4500],
            ['nombre' => 'Roberto Díaz',    'telefono' => '3425-654321', 'saldo_deudor' => 1200],
            ['nombre' => 'Lucía Fernández', 'telefono' => null,          'saldo_deudor' =>    0],
        ];

        foreach ($clientes as $c) {
            Customer::create($c);
        }
    }
}
