<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerformanceSeeder extends Seeder
{
    // ── Configuración ─────────────────────────────────────────────
    const TIENDAS       = 3;
    const MESES         = 6;    // últimos N meses de datos
    const VENTAS_POR_DIA = 25;  // promedio por tienda
    const CLIENTES_POR_TIENDA   = 40;
    const PRODUCTOS_POR_TIENDA  = 50;
    const EMPLEADOS_POR_TIENDA  = 4;

    public function run(): void
    {
        $this->command->info('🏪 Creando tiendas...');
        $tiendas = $this->crearTiendas();

        $this->command->info('🛍️ Creando productos...');
        $productos = $this->crearProductos($tiendas);

        $this->command->info('👥 Creando clientes...');
        $clientes = $this->crearClientes($tiendas);

        $this->command->info('👤 Creando empleados...');
        $empleados = $this->crearEmpleados($tiendas);

        $this->command->info('🧾 Generando ventas (esto puede tardar un momento)...');
        $this->generarVentas($tiendas, $productos, $clientes, $empleados);

        $this->command->info('✅ Performance seeder completo.');
        $this->command->table(
            ['Tienda', 'Productos', 'Clientes', 'Empleados', 'Órdenes'],
            $tiendas->map(fn($t) => [
                $t->nombre,
                Product::withoutGlobalScope('tienda')->where('tienda_id', $t->id)->count(),
                Customer::withoutGlobalScope('tienda')->where('tienda_id', $t->id)->count(),
                User::where('tienda_id', $t->id)->whereIn('rol',['admin','empleado'])->count(),
                Order::withoutGlobalScope('tienda')->where('tienda_id', $t->id)->count(),
            ])->toArray()
        );
    }

    // ─────────────────────────────────────────────────────────────
    private function crearTiendas()
    {
        $datos = [
            ['nombre' => 'Kiosco Central',    'ciudad' => 'Santa Fe',   'direccion' => 'San Martín 1234'],
            ['nombre' => 'Verdulería El Sol',  'ciudad' => 'Rosario',    'direccion' => 'Pellegrini 567'],
            ['nombre' => 'Almacén Don Pedro', 'ciudad' => 'Paraná',     'direccion' => 'Urquiza 890'],
        ];

        return collect(array_slice($datos, 0, self::TIENDAS))->map(function ($d) {
            $tienda = Tienda::create(array_merge($d, ['activa' => true]));

            // Admin de la tienda
            User::create([
                'name'      => "Admin {$tienda->nombre}",
                'email'     => 'admin.' . \Str::slug($tienda->nombre) . '@test.local',
                'password'  => Hash::make('test1234'),
                'rol'       => 'admin',
                'tienda_id' => $tienda->id,
                'activo'    => true,
            ]);

            // Cliente genérico
            Customer::create([
                'tienda_id'    => $tienda->id,
                'nombre'       => 'Cuenta Genérica',
                'saldo_deudor' => 0,
            ]);

            return $tienda;
        });
    }

    // ─────────────────────────────────────────────────────────────
    private function crearProductos($tiendas)
    {
        $catalogo = [
            // [nombre, costo, venta, stock]
            ['Coca Cola 500ml',      800,  1200, 48],
            ['Coca Cola 1.5L',      1500,  2200, 24],
            ['Sprite 500ml',         800,  1200, 30],
            ['Fanta 500ml',          800,  1200, 24],
            ['Agua Mineral 500ml',   300,   500, 60],
            ['Agua Mineral 1.5L',    500,   900, 30],
            ['Cerveza Quilmes 1L',  1800,  2800, 24],
            ['Cerveza Corona 710ml',2200,  3500, 18],
            ['Vino Malbec 750ml',   3000,  4500, 12],
            ['Jugo Cepita 1L',       900,  1400, 24],
            ['Leche La Serenísima', 1100,  1700, 30],
            ['Yogur Ser 190g',       600,   950, 20],
            ['Manteca La Paulina',  1400,  2100, 15],
            ['Queso Cremoso x100g',  800,  1300, 10],
            ['Jamón Cocido x100g',  1000,  1600, 8],
            ['Pan Lactal Bimbo',     900,  1400, 12],
            ['Facturas x6',          900,  1500, 20],
            ['Medialunas x6',        700,  1200, 20],
            ['Alfajor Havanna',      900,  1400, 30],
            ['Alfajor Milka',        600,   950, 40],
            ['Chips 100g',           500,   850, 25],
            ['Maní con chocolate',   400,   700, 30],
            ['Chicles Beldent',      200,   400, 60],
            ['Caramelos Mentos',     300,   500, 40],
            ['Galletas Oreo',        800,  1300, 20],
            ['Cigarrillos Marlboro',2500,  3500, 20],
            ['Cigarrillos Lucky',   2000,  2800, 20],
            ['Tomate x kg',          600,   950, 8],
            ['Papa x kg',            400,   650, 10],
            ['Cebolla x kg',         350,   600, 8],
            ['Zanahoria x kg',       300,   550, 6],
            ['Banana x kg',          500,   800, 7],
            ['Manzana x kg',         700,  1100, 6],
            ['Naranja x kg',         500,   850, 8],
            ['Limón x kg',           400,   700, 5],
            ['Lechuga',              400,   700, 10],
            ['Tomate Cherry 250g',   700,  1200, 8],
            ['Palta',                600,  1000, 6],
            ['Fideos Matarazzo 500g',500,   850, 20],
            ['Arroz SOS 1kg',        700,  1100, 15],
            ['Aceite Natura 900ml', 2000,  2800, 10],
            ['Azúcar Ledesma 1kg',   600,   950, 15],
            ['Harina Pureza 1kg',    500,   850, 12],
            ['Sal Celusal 500g',     300,   500, 20],
            ['Jabón Dove',          1200,  1800, 10],
            ['Shampoo Pantene',     1800,  2600, 8],
            ['Papel higiénico x4',  1500,  2200, 12],
            ['Lavandina 1L',         500,   850, 15],
            ['Detergente 500ml',     600,   950, 10],
            ['Escoba',              2000,  3000, 5],
        ];

        $porTienda = [];
        foreach ($tiendas as $tienda) {
            $porTienda[$tienda->id] = collect();
            // Tomar los primeros N productos del catálogo para esta tienda
            $seleccion = array_slice($catalogo, 0, self::PRODUCTOS_POR_TIENDA);
            foreach ($seleccion as $p) {
                $prod = Product::create([
                    'tienda_id'     => $tienda->id,
                    'nombre'        => $p[0],
                    'precio_costo'  => $p[1],
                    'precio_venta'  => $p[2],
                    'stock'         => $p[3],
                    'activo'        => true,
                ]);
                $porTienda[$tienda->id]->push($prod);
            }
        }
        return $porTienda;
    }

    // ─────────────────────────────────────────────────────────────
    private function crearClientes($tiendas)
    {
        $nombres = [
            'Juan Pérez','María González','Carlos Rodríguez','Ana Martínez',
            'Luis García','Laura Fernández','Diego López','Sofía Díaz',
            'Miguel Torres','Valentina Ruiz','Martín Sánchez','Florencia Moreno',
            'Pablo Romero','Camila Herrera','Sebastián Jiménez','Lucía Medina',
            'Nicolás Castro','Agustina Vargas','Facundo Ramos','Natalia Silva',
            'Rodrigo Molina','Paola Ortega','Gustavo Suárez','Daniela Reyes',
            'Tomás Flores','Cecilia Mendez','Ignacio Pereyra','Valeria Ibáñez',
            'Ramiro Acosta','Noelia Vega','Leandro Fuentes','Andrea Paredes',
            'Ezequiel Guerrero','Patricia Salinas','Matías Ríos','Lorena Aguilar',
            'Claudio Peña','Silvia Espinoza','Germán Cabrera','Paula Núñez',
        ];

        $porTienda = [];
        foreach ($tiendas as $tienda) {
            $porTienda[$tienda->id] = collect();
            $cantidad = min(self::CLIENTES_POR_TIENDA, count($nombres));
            shuffle($nombres);
            for ($i = 0; $i < $cantidad; $i++) {
                $deuda = rand(0, 10) > 7 ? rand(500, 8000) : 0;
                $c = Customer::create([
                    'tienda_id'    => $tienda->id,
                    'nombre'       => $nombres[$i],
                    'telefono'     => '342' . rand(4000000, 4999999),
                    'saldo_deudor' => $deuda,
                    'activo'       => true,
                ]);
                $porTienda[$tienda->id]->push($c);
            }
        }
        return $porTienda;
    }

    // ─────────────────────────────────────────────────────────────
    private function crearEmpleados($tiendas)
    {
        $porTienda = [];
        foreach ($tiendas as $tienda) {
            $porTienda[$tienda->id] = collect();
            for ($i = 1; $i <= self::EMPLEADOS_POR_TIENDA; $i++) {
                $e = User::create([
                    'name'      => "Empleado {$i} — {$tienda->nombre}",
                    'email'     => "emp{$i}." . \Str::slug($tienda->nombre) . '@test.local',
                    'password'  => Hash::make('test1234'),
                    'rol'       => 'empleado',
                    'tienda_id' => $tienda->id,
                    'activo'    => $i < self::EMPLEADOS_POR_TIENDA, // último inactivo para testear
                ]);
                $porTienda[$tienda->id]->push($e);
            }
        }
        return $porTienda;
    }

    // ─────────────────────────────────────────────────────────────
    private function generarVentas($tiendas, $productos, $clientes, $empleados)
    {
        $metodos = ['efectivo', 'efectivo', 'efectivo', 'transferencia', 'fiado']; // pesos

        foreach ($tiendas as $tienda) {
            $prods   = $productos[$tienda->id];
            $clts    = $clientes[$tienda->id];
            $emps    = $empleados[$tienda->id]->where('activo', true)->values();

            $inicio = now()->subMonths(self::MESES)->startOfDay();
            $hoy    = now()->endOfDay();

            $fecha = $inicio->copy();

            while ($fecha <= $hoy) {
                // Menos ventas los domingos
                $cantidad = $fecha->dayOfWeek === 0
                    ? (int)(self::VENTAS_POR_DIA * 0.4)
                    : rand((int)(self::VENTAS_POR_DIA * 0.7), (int)(self::VENTAS_POR_DIA * 1.3));

                // Caja del día
                $aperturaHora = $fecha->copy()->setTime(rand(7,9), rand(0,59));
                $cierreHora   = $fecha->copy()->setTime(rand(19,22), rand(0,59));

                // No crear caja para hoy (para que el test tenga caja abierta)
                $cajaId = null;
                if ($fecha->toDateString() !== now()->toDateString()) {
                    $caja = Caja::create([
                        'tienda_id'      => $tienda->id,
                        'user_id'        => $emps->first()->id,
                        'monto_apertura' => rand(1000, 5000),
                        'monto_cierre'   => rand(15000, 80000),
                        'total_efectivo' => rand(10000, 50000),
                        'total_transfer' => rand(2000, 20000),
                        'total_fiado'    => rand(1000, 10000),
                        'abierta_at'     => $aperturaHora,
                        'cerrada_at'     => $cierreHora,
                        'notas_cierre'   => rand(0,1) ? 'Turno sin novedades.' : null,
                        'created_at'     => $aperturaHora,
                        'updated_at'     => $cierreHora,
                    ]);
                    $cajaId = $caja->id;
                }

                for ($v = 0; $v < $cantidad; $v++) {
                    $metodo   = $metodos[array_rand($metodos)];
                    $empleado = $emps->random();
                    $horaVenta = $fecha->copy()->setTime(rand(8,21), rand(0,59), rand(0,59));

                    $customerId = null;
                    if ($metodo === 'fiado') {
                        $customerId = $clts->random()->id;
                    }

                    // 2 a 5 productos por venta
                    $cantItems = rand(2, 5);
                    $items = $prods->random(min($cantItems, $prods->count()));
                    $total = 0;
                    $lineas = [];

                    foreach ($items as $prod) {
                        $cant     = rand(1, 3);
                        $precio   = $prod->precio_venta;
                        $subtotal = $precio * $cant;
                        $total   += $subtotal;
                        $lineas[] = ['product' => $prod, 'cant' => $cant, 'precio' => $precio, 'subtotal' => $subtotal];
                    }

                    $order = Order::create([
                        'tienda_id'   => $tienda->id,
                        'user_id'     => $empleado->id,
                        'customer_id' => $customerId,
                        'total'       => $total,
                        'metodo_pago' => $metodo,
                        'created_at'  => $horaVenta,
                        'updated_at'  => $horaVenta,
                    ]);

                    foreach ($lineas as $l) {
                        OrderItem::create([
                            'order_id'        => $order->id,
                            'product_id'      => $l['product']->id,
                            'cantidad'        => $l['cant'],
                            'precio_unitario' => $l['precio'],
                            'subtotal'        => $l['subtotal'],
                            'created_at'      => $horaVenta,
                            'updated_at'      => $horaVenta,
                        ]);
                    }
                }

                $fecha->addDay();
            }

            $this->command->info("  ✓ {$tienda->nombre}");
        }
    }
}
