<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Order;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    /**
     * Pantalla principal de caja — muestra si hay una caja abierta o no
     */
    public function index()
    {
        // Buscar caja abierta del día actual
        $cajaAbierta = Caja::where('user_id', auth()->id())
            ->whereNull('cerrada_at')
            ->latest()
            ->first();

        // Historial de cajas cerradas (últimas 30)
        $historial = Caja::with('user')
            ->whereNotNull('cerrada_at')
            ->orderByDesc('cerrada_at')
            ->limit(30)
            ->get();

        return view('cajas.index', compact('cajaAbierta', 'historial'));
    }

    /**
     * Abrir caja — registrar monto inicial en efectivo
     */
    public function abrir(Request $request)
    {
        // No permitir abrir si ya hay una abierta
        $yaAbierta = Caja::where('user_id', auth()->id())
            ->whereNull('cerrada_at')
            ->exists();

        if ($yaAbierta) {
            return back()->with('error', 'Ya tenés una caja abierta.');
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        Caja::create([
            'user_id'        => auth()->id(),
            'monto_apertura' => $request->monto_apertura,
            'abierta_at'     => now(),
        ]);

        return redirect()->route('cajas.index')
            ->with('success', '✅ Caja abierta con $' . number_format($request->monto_apertura, 0, ',', '.'));
    }

    /**
     * Cerrar caja — calcular totales del turno y registrar monto real
     */
    public function cerrar(Request $request, Caja $caja)
    {
        if (!$caja->estaAbierta()) {
            return back()->with('error', 'Esta caja ya fue cerrada.');
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'notas_cierre' => 'nullable|string|max:500',
        ]);

        // Calcular ventas desde la apertura de esta caja
        $ventas = Order::where('created_at', '>=', $caja->abierta_at)
            ->selectRaw("
                SUM(CASE WHEN metodo_pago = 'efectivo'      THEN total ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago = 'transferencia' THEN total ELSE 0 END) as transfer,
                SUM(CASE WHEN metodo_pago = 'fiado'         THEN total ELSE 0 END) as fiado
            ")
            ->first();

        $caja->update([
            'monto_cierre'  => $request->monto_cierre,
            'total_efectivo'=> $ventas->efectivo  ?? 0,
            'total_transfer'=> $ventas->transfer  ?? 0,
            'total_fiado'   => $ventas->fiado     ?? 0,
            'notas_cierre'  => $request->notas_cierre,
            'cerrada_at'    => now(),
        ]);

        $dif = $caja->fresh()->diferencia();
        $msg = $dif == 0
            ? '✅ Caja cerrada. Sin diferencias.'
            : ($dif > 0
                ? "✅ Caja cerrada. Sobraron $" . number_format(abs($dif), 2, ',', '.')
                : "⚠️ Caja cerrada. Faltan $"  . number_format(abs($dif), 2, ',', '.'));

        return redirect()->route('cajas.index')->with('success', $msg);
    }
}
