<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TiendaController extends Controller
{
    // ── Lista de tiendas (solo superadmin) ────────────────────────
    public function index()
    {
        $tiendas = Tienda::withCount(['users', 'products', 'orders'])->get();
        return view('tiendas.index', compact('tiendas'));
    }

    // ── Formulario nueva tienda ───────────────────────────────────
    public function create()
    {
        return view('tiendas.create');
    }

    // ── Crear tienda ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'ciudad'    => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'telefono'  => 'nullable|string|max:30',
            'logo'      => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $tienda = Tienda::create([
            'nombre'    => $request->nombre,
            'ciudad'    => $request->ciudad,
            'direccion' => $request->direccion,
            'telefono'  => $request->telefono,
            'logo_path' => $logoPath,
        ]);

        return redirect()->route('tiendas.index')
            ->with('success', "✅ Tienda \"{$tienda->nombre}\" creada.");
    }

    // ── Formulario edición ────────────────────────────────────────
    public function edit(Tienda $tienda)
    {
        // admin solo puede editar su propia tienda
        if (!auth()->user()->esSuperAdmin() && auth()->user()->tienda_id !== $tienda->id) {
            abort(403);
        }
        return view('tiendas.edit', compact('tienda'));
    }

    // ── Guardar cambios ───────────────────────────────────────────
    public function update(Request $request, Tienda $tienda)
    {
        if (!auth()->user()->esSuperAdmin() && auth()->user()->tienda_id !== $tienda->id) {
            abort(403);
        }

        $request->validate([
            'nombre'    => 'required|string|max:100',
            'ciudad'    => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'telefono'  => 'nullable|string|max:30',
            'logo'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'ciudad', 'direccion', 'telefono']);

        if ($request->hasFile('logo')) {
            // Borrar logo viejo
            if ($tienda->logo_path) Storage::disk('public')->delete($tienda->logo_path);
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->boolean('borrar_logo') && $tienda->logo_path) {
            Storage::disk('public')->delete($tienda->logo_path);
            $data['logo_path'] = null;
        }

        $tienda->update($data);

        return back()->with('success', '✅ Configuración guardada.');
    }

    // ── Eliminar tienda (solo superadmin) ─────────────────────────
    public function destroy(Tienda $tienda)
    {
        if ($tienda->logo_path) Storage::disk('public')->delete($tienda->logo_path);
        $tienda->delete();

        return redirect()->route('tiendas.index')
            ->with('success', "Tienda eliminada.");
    }

    // ── Cambiar tienda activa (superadmin) ────────────────────────
    public function switchTienda(Request $request)
    {
        $request->validate(['tienda_id' => 'required|exists:tiendas,id']);
        session(['tienda_activa_id' => $request->tienda_id]);
        return back()->with('success', '✅ Tienda cambiada.');
    }
}
