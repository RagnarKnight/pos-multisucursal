<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Solo superadmin llega aquí (ver ruta)
        $tiendas = Tienda::where('activa', true)
            ->withCount(['users', 'products'])
            ->get();

        $hoy = today()->toDateString();

        // ── Métricas por tienda (hoy) ─────────────────────────────────
        $metricasPorTienda = $tiendas->map(function ($tienda) use ($hoy) {

            // Ventas de hoy SIN global scope (superadmin ve todo)
            $ordenes = Order::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->whereDate('created_at', $hoy)
                ->get();

            $cajaAbierta = Caja::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->whereNull('cerrada_at')
                ->latest('abierta_at')
                ->first();

            $deudoresTop = Customer::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->where('saldo_deudor', '>', 0)
                ->orderByDesc('saldo_deudor')
                ->limit(3)
                ->get();

            $totalDeuda = Customer::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->sum('saldo_deudor');

            $sinStock = Product::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->where('activo', true)
                ->where('stock', 0)
                ->count();

            $stockBajo = Product::withoutGlobalScope('tienda')
                ->where('tienda_id', $tienda->id)
                ->where('activo', true)
                ->where('stock', '>', 0)
                ->where('stock', '<=', 3)
                ->count();

            return [
                'tienda'       => $tienda,
                'ventas_hoy'   => $ordenes->whereIn('metodo_pago', ['efectivo','transferencia'])->sum('total'),
                'fiado_hoy'    => $ordenes->where('metodo_pago', 'fiado')->sum('total'),
                'ordenes_hoy'  => $ordenes->count(),
                'caja_abierta' => $cajaAbierta,
                'total_deuda'  => $totalDeuda,
                'deudores_top' => $deudoresTop,
                'sin_stock'    => $sinStock,
                'stock_bajo'   => $stockBajo,
            ];
        });

        // ── Totales consolidados ──────────────────────────────────────
        $consolidado = [
            'ventas_hoy'  => $metricasPorTienda->sum('ventas_hoy'),
            'fiado_hoy'   => $metricasPorTienda->sum('fiado_hoy'),
            'ordenes_hoy' => $metricasPorTienda->sum('ordenes_hoy'),
            'total_deuda' => $metricasPorTienda->sum('total_deuda'),
            'sin_stock'   => $metricasPorTienda->sum('sin_stock'),
        ];

        // ── Ventas últimos 7 días (todas las tiendas) ─────────────────
        $ultimos7 = Order::withoutGlobalScope('tienda')
            ->selectRaw("DATE(created_at) as dia, tienda_id, SUM(total) as total")
            ->whereIn('metodo_pago', ['efectivo','transferencia'])
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('dia', 'tienda_id')
            ->orderBy('dia')
            ->get();

        // ── Superadmins del sistema ───────────────────────────────────
        $superadmins = User::where('rol', 'superadmin')->orderBy('name')->get();

        return view('dashboard.index', compact(
            'tiendas', 'metricasPorTienda', 'consolidado', 'ultimos7', 'superadmins'
        ));
    }
}
