<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // ── Usuario desactivado: bloquear aunque las credenciales sean válidas
        if (!auth()->user()->activo) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta está desactivada. Contactá al administrador.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // ── Registrar sesión activa para control de sesión única ──
        $sessionId = $request->session()->getId();
        \Illuminate\Support\Facades\Cache::put(
            'session_activa_' . auth()->id(),
            $sessionId,
            now()->addDays(7)
        );

        // Superadmin → dashboard | resto → POS
        return auth()->user()->esSuperAdmin()
            ? redirect()->route('dashboard')
            : redirect()->intended(route('pos.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // ── Limpiar sesión registrada en caché al hacer logout ──
        if (auth()->check()) {
            \Illuminate\Support\Facades\Cache::forget('session_activa_' . auth()->id());
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
