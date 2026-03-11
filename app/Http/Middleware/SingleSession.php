<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SingleSession
{
    /**
     * Al loguear guardamos session_id en caché asociado al user_id.
     * En cada request verificamos que la sesión actual sea la última registrada.
     * Si otro dispositivo logueó después, este middleware desloguea al anterior.
     *
     * Requiere CACHE_STORE=file (o cualquier driver compartido).
     * Con SESSION_DRIVER=file funciona perfecto en hardware limitado.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user       = auth()->user();
        $cacheKey   = "session_activa_{$user->id}";
        $sessionId  = $request->session()->getId();

        $sessionRegistrada = Cache::get($cacheKey);

        if ($sessionRegistrada === null) {
            // Primera vez que este usuario pasa por aquí — registrar
            Cache::put($cacheKey, $sessionId, now()->addDays(7));

        } elseif ($sessionRegistrada !== $sessionId) {
            // Hay otra sesión más nueva — esta quedó desplazada
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' =>
                    'Tu sesión fue cerrada porque iniciaste sesión desde otro dispositivo.'
                ]);
        }

        return $next($request);
    }
}
