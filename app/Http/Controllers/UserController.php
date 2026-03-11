<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $yo = auth()->user();

        if ($yo->esSuperAdmin()) {
            $users   = User::with('tienda')
                ->orderByRaw("CASE rol WHEN 'superadmin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
                ->orderBy('name')->get();
            $tiendas = Tienda::where('activa', true)->get();
        } else {
            $users   = User::where('tienda_id', $yo->tienda_id)
                ->orderByRaw("CASE rol WHEN 'admin' THEN 0 ELSE 1 END")
                ->orderBy('name')->get();
            $tiendas = collect([$yo->tienda]);
        }

        return view('users.index', compact('users', 'tiendas'));
    }

    public function create()
    {
        $yo      = auth()->user();
        $tiendas = $yo->esSuperAdmin()
            ? Tienda::where('activa', true)->orderBy('nombre')->get()
            : collect([$yo->tienda]);

        $rolesDisponibles = $yo->esSuperAdmin()
            ? ['superadmin', 'admin', 'empleado']
            : ['admin', 'empleado'];

        return view('users.create', compact('tiendas', 'rolesDisponibles'));
    }

    public function store(Request $request)
    {
        $yo           = auth()->user();
        $rolesValidos = $yo->esSuperAdmin() ? 'in:superadmin,admin,empleado' : 'in:admin,empleado';

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(8)],
            'rol'       => ['required', $rolesValidos],
            'tienda_id' => 'nullable|exists:tiendas,id',
        ]);

        $tiendaId = null;
        if ($request->rol !== 'superadmin') {
            $tiendaId = $yo->esSuperAdmin() ? $request->tienda_id : $yo->tienda_id;
        }

        if (!$yo->esSuperAdmin() && $tiendaId !== $yo->tienda_id) abort(403);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'rol'       => $request->rol,
            'tienda_id' => $tiendaId,
            'activo'    => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "✅ Usuario \"{$request->name}\" creado.");
    }

    public function edit(User $user)
    {
        $yo = auth()->user();
        if (!$yo->esSuperAdmin() && $user->tienda_id !== $yo->tienda_id) abort(403);
        if ($user->esSuperAdmin() && !$yo->esSuperAdmin()) abort(403);

        $tiendas = $yo->esSuperAdmin()
            ? Tienda::where('activa', true)->orderBy('nombre')->get()
            : collect([$yo->tienda]);

        $rolesDisponibles = $yo->esSuperAdmin()
            ? ['superadmin', 'admin', 'empleado']
            : ['admin', 'empleado'];

        return view('users.edit', compact('user', 'tiendas', 'rolesDisponibles'));
    }

    public function update(Request $request, User $user)
    {
        $yo = auth()->user();
        if (!$yo->esSuperAdmin() && $user->tienda_id !== $yo->tienda_id) abort(403);
        if ($user->esSuperAdmin() && !$yo->esSuperAdmin()) abort(403);

        $rolesValidos = $yo->esSuperAdmin() ? 'in:superadmin,admin,empleado' : 'in:admin,empleado';

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'rol'       => ['required', $rolesValidos],
            'tienda_id' => 'nullable|exists:tiendas,id',
            'password'  => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $tiendaId = $user->tienda_id;
        if ($yo->esSuperAdmin()) {
            $tiendaId = $request->rol === 'superadmin' ? null : $request->tienda_id;
        }

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'rol'       => $request->rol,
            'tienda_id' => $tiendaId,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "✅ Usuario \"{$user->name}\" actualizado.");
    }

    // ── Activar / Desactivar (toggle) ─────────────────────────────
    public function toggleActivo(User $user)
    {
        $yo = auth()->user();

        if ($user->id === $yo->id) {
            return back()->with('error', 'No podés desactivarte a vos mismo.');
        }
        if (!$yo->esSuperAdmin() && $user->tienda_id !== $yo->tienda_id) abort(403);
        if ($user->esSuperAdmin() && !$yo->esSuperAdmin()) abort(403);

        $user->update(['activo' => !$user->activo]);

        $estado = $user->activo ? 'activado' : 'desactivado';
        return back()->with('success', "✅ Usuario \"{$user->name}\" {$estado}.");
    }

    // ── Eliminar — solo si NO tiene ventas ────────────────────────
    public function destroy(User $user)
    {
        $yo = auth()->user();

        if ($user->id === $yo->id) {
            return back()->with('error', 'No podés eliminarte a vos mismo.');
        }
        if (!$yo->esSuperAdmin() && $user->tienda_id !== $yo->tienda_id) abort(403);
        if ($user->esSuperAdmin() && !$yo->esSuperAdmin()) abort(403);

        // Verificar si tiene órdenes asociadas
        if ($user->orders()->exists()) {
            return back()->with('error',
                "⚠️ \"{$user->name}\" tiene ventas registradas y no puede eliminarse. Podés desactivarlo para que no pueda iniciar sesión."
            );
        }

        $nombre = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Usuario \"{$nombre}\" eliminado.");
    }
}
