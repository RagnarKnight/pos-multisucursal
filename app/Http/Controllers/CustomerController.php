<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Lista de clientes deudores — "La Libreta"
     */
    public function index()
    {
        $customers = Customer::orderByDesc('saldo_deudor')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'notas'    => 'nullable|string',
        ]);

        Customer::create($request->only('nombre', 'telefono', 'notas'));

        return redirect()->route('customers.index')
            ->with('success', 'Cliente agregado a la libreta.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.items.product']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'notas'    => 'nullable|string',
        ]);

        $customer->update($request->only('nombre', 'telefono', 'notas'));

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado.');
    }

    /**
     * Saldar cuenta — Pago parcial o total de la deuda
     */
    public function saldar(Request $request, Customer $customer)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $customer->saldo_deudor,
        ]);

        $customer->saldar($request->monto);

        $mensaje = $customer->debeAlgo()
            ? "Pago registrado. Saldo restante: $" . number_format($customer->fresh()->saldo_deudor, 2)
            : "✅ {$customer->nombre} saldó su cuenta completa.";

        return back()->with('success', $mensaje);
    }
}
