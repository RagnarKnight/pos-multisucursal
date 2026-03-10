<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $mes  = $request->get('mes',  now()->month);
        $anio = $request->get('anio', now()->year);

        // ── Ventas del mes por día ─────────────────────────────────
        $ventasPorDia = Order::selectRaw("
                date(created_at) as fecha,
                SUM(total) as total,
                COUNT(*) as cantidad,
                SUM(CASE WHEN metodo_pago = 'efectivo'     THEN total ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago = 'transferencia'THEN total ELSE 0 END) as transferencia,
                SUM(CASE WHEN metodo_pago = 'fiado'        THEN total ELSE 0 END) as fiado
            ")
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->groupByRaw("date(created_at)")
            ->orderByRaw("date(created_at)")
            ->get();

        // ── Totales del mes ────────────────────────────────────────
        $totalesMes = [
            'efectivo'      => $ventasPorDia->sum('efectivo'),
            'transferencia' => $ventasPorDia->sum('transferencia'),
            'fiado'         => $ventasPorDia->sum('fiado'),
            'total'         => $ventasPorDia->sum('total'),
            'ordenes'       => $ventasPorDia->sum('cantidad'),
            'ticket_prom'   => $ventasPorDia->sum('cantidad') > 0
                               ? $ventasPorDia->sum('total') / $ventasPorDia->sum('cantidad')
                               : 0,
        ];

        // ── Top 10 productos más vendidos (por cantidad) ───────────
        $topProductos = OrderItem::select('product_id',
                DB::raw('SUM(cantidad) as total_unidades'),
                DB::raw('SUM(subtotal) as total_ingresos')
            )
            ->whereHas('order', fn($q) => $q
                ->whereMonth('created_at', $mes)
                ->whereYear('created_at', $anio)
            )
            ->with('product:id,nombre,precio_venta,precio_costo')
            ->groupBy('product_id')
            ->orderByDesc('total_unidades')
            ->limit(10)
            ->get();

        // ── Clientes con más deuda ─────────────────────────────────
        $topDeudores = Customer::where('saldo_deudor', '>', 0)
            ->orderByDesc('saldo_deudor')
            ->limit(5)
            ->get();

        // ── Productos sin stock ────────────────────────────────────
        $sinStock = Product::where('stock', 0)->where('activo', true)->get();

        // ── Datos para el gráfico de barras (JSON para JS) ────────
        $graficoDias = [
            'labels'        => $ventasPorDia->pluck('fecha')
                                ->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))
                                ->toArray(),
            'efectivo'      => $ventasPorDia->pluck('efectivo')->map(fn($v) => (float)$v)->toArray(),
            'transferencia' => $ventasPorDia->pluck('transferencia')->map(fn($v) => (float)$v)->toArray(),
            'fiado'         => $ventasPorDia->pluck('fiado')->map(fn($v) => (float)$v)->toArray(),
        ];

        // Lista de meses y años para el selector
        $meses = [
            1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
            5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
            9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre',
        ];
        $anios = range(now()->year, now()->year - 2);

        return view('reports.index', compact(
            'ventasPorDia', 'totalesMes', 'topProductos',
            'topDeudores', 'sinStock', 'graficoDias',
            'mes', 'anio', 'meses', 'anios'
        ));
    }
}
