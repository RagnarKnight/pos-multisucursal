<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Historial de ventas — Cierre de caja diario
     */
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', today()->toDateString());

        // Cajas abiertas ese día
        $cajasDia = \App\Models\Caja::whereDate('abierta_at', $fecha)->get();

        $todosOrders = Order::with(['user', 'customer', 'items.product'])
            ->whereDate('created_at', $fecha)
            ->latest()
            ->get();

        // Separar órdenes según si caen dentro del horario de alguna caja
        $orders = $todosOrders->filter(function ($order) use ($cajasDia) {
            foreach ($cajasDia as $caja) {
                $cierre = $caja->cerrada_at ?? now();
                if ($order->created_at->between($caja->abierta_at, $cierre)) {
                    return true;
                }
            }
            return false;
        })->values();

        $ordersSinCaja = $todosOrders->diff($orders)->values();

        $resumen = [
            'total_efectivo'      => $todosOrders->where('metodo_pago', 'efectivo')->sum('total'),
            'total_transferencia' => $todosOrders->where('metodo_pago', 'transferencia')->sum('total'),
            'total_fiado'         => $todosOrders->where('metodo_pago', 'fiado')->sum('total'),
            'total_dia'           => $todosOrders->whereIn('metodo_pago', ['efectivo', 'transferencia'])->sum('total'),
        ];

        return view('orders.index', compact('orders', 'ordersSinCaja', 'resumen', 'fecha'));
    }

    /**
     * Vista POS — Cuadrícula de productos
     */
    public function create()
    {
        $products  = Product::activos()->orderBy('nombre')->get();
        $customers = Customer::orderBy('nombre')->get();

        return view('pos.index', compact('products', 'customers'));
    }

    /**
     * Procesar venta — El corazón del sistema
     *
     * Regla de negocio clave:
     *   si metodo_pago === 'fiado' → se debe pasar customer_id
     *   y se incrementa saldo_deudor del cliente
     */
    public function store(Request $request)
    {
        // Validar: en fiado se necesita customer_id existente O nuevo_cliente_nombre
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.cantidad'       => 'required|integer|min:1',
            'metodo_pago'            => 'required|in:efectivo,transferencia,fiado',
            'customer_id'            => 'nullable|exists:customers,id',
            'nuevo_cliente_nombre'   => 'nullable|string|max:100',
            'comprobante'            => 'nullable|image|max:2048',
        ]);

        // Si es fiado debe tener uno de los dos
        if ($request->metodo_pago === 'fiado'
            && !$request->customer_id
            && !filled($request->nuevo_cliente_nombre)) {
            return back()->withErrors(['customer_id' => 'Seleccioná un cliente o ingresá un nombre nuevo.'])
                         ->withInput();
        }

        DB::transaction(function () use ($request) {

            // 1. Subir comprobante de transferencia (si existe)
            $comprobantePath = null;
            if ($request->hasFile('comprobante')) {
                $comprobantePath = $request->file('comprobante')
                    ->store('comprobantes', 'public');
            }

            // 2. Crear cliente nuevo on-the-fly si corresponde
            $customerId = $request->customer_id;
            if ($request->metodo_pago === 'fiado' && filled($request->nuevo_cliente_nombre)) {
                $nuevoCliente = Customer::create([
                    'nombre'       => trim($request->nuevo_cliente_nombre),
                    'saldo_deudor' => 0,
                ]);
                $customerId = $nuevoCliente->id;
            }

            // 3. Calcular total y validar stock producto a producto
            $total  = 0;
            $lineas = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$product->nombre}");
                }

                $subtotal = $product->precio_venta * $item['cantidad'];
                $total   += $subtotal;

                $lineas[] = [
                    'product'         => $product,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $product->precio_venta,
                    'subtotal'        => $subtotal,
                ];
            }

            // 4. Crear la Order
            $order = Order::create([
                'user_id'          => auth()->id(),
                'customer_id'      => $customerId,
                'total'            => $total,
                'metodo_pago'      => $request->metodo_pago,
                'comprobante_path' => $comprobantePath,
                'notas'            => $request->notas,
            ]);

            // 5. Crear OrderItems y descontar stock
            foreach ($lineas as $linea) {
                $order->items()->create([
                    'product_id'      => $linea['product']->id,
                    'cantidad'        => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'subtotal'        => $linea['subtotal'],
                ]);
                $linea['product']->decrement('stock', $linea['cantidad']);
            }

            // 6. LÓGICA DE FIADO — sumar deuda al cliente
            if ($request->metodo_pago === 'fiado' && $customerId) {
                Customer::findOrFail($customerId)->agregarDeuda($total);
            }
        });

        $redirectUrl = route('pos.index');

        // FormData fetch desde móvil (X-Requested-With: XMLHttpRequest)
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl)
            ->with('success', '✅ Venta registrada correctamente.');
    }

    /**
     * Detalle de una orden
     */
    public function show(Order $order)
    {
        $order->load(['user', 'customer', 'items.product']);
        $customers = Customer::orderBy('nombre')->get();
        return view('orders.show', compact('order', 'customers'));
    }

    /**
     * Subir o reemplazar comprobante de transferencia desde el historial
     */
    public function subirComprobante(Request $request, Order $order)
    {
        if ($order->metodo_pago !== 'transferencia') {
            return back()->with('error', 'Solo se puede subir comprobante en ventas por transferencia.');
        }

        $request->validate([
            'comprobante' => 'required|image|max:5120', // 5MB
        ]);

        // Borrar el anterior si existe
        if ($order->comprobante_path) {
            Storage::disk('public')->delete($order->comprobante_path);
        }

        $path = $request->file('comprobante')->store('comprobantes', 'public');
        $order->update(['comprobante_path' => $path]);

        return back()->with('success', '✅ Comprobante guardado correctamente.');
    }

    /**
     * Editar cliente asignado a una orden fiada
     * Reasigna la deuda del cliente anterior al nuevo
     */
    public function update(Request $request, Order $order)
    {
        // Solo órdenes fiadas pueden editarse desde aquí
        if ($order->metodo_pago !== 'fiado') {
            return back()->with('error', 'Solo se pueden editar órdenes fiadas.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        DB::transaction(function () use ($request, $order) {
            $nuevoClienteId = $request->customer_id;

            // Si cambió el cliente, mover la deuda
            if ($order->customer_id !== (int) $nuevoClienteId) {

                // Quitar deuda al cliente anterior (si existía)
                if ($order->customer_id) {
                    $anterior = Customer::find($order->customer_id);
                    if ($anterior) {
                        $anterior->saldar($order->total);
                    }
                }

                // Agregar deuda al cliente nuevo
                $nuevo = Customer::findOrFail($nuevoClienteId);
                $nuevo->agregarDeuda($order->total);

                $order->update(['customer_id' => $nuevoClienteId]);
            }
        });

        return back()->with('success', '✅ Cliente de la orden actualizado correctamente.');
    }
}
