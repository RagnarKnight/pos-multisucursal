<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111314">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mi Negocio">
    <meta name="mobile-web-app-capable" content="yes">

    {{-- PWA manifest --}}
    <link rel="manifest" href="/manifest.json">

    {{-- Íconos Apple (iOS no usa manifest para esto) --}}
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>Mi Negocio — Iniciar sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --c-bg:      #111314;
            --c-surface: #1c1f21;
            --c-border:  #2e3235;
            --c-accent:  #f5a623;
            --c-text:    #eaeaea;
            --c-muted:   #7a8085;
            --font-display: 'Barlow Condensed', sans-serif;
            --font-body:    'Barlow', sans-serif;
        }

        html, body {
            height: 100%;
            background: var(--c-bg);
            color: var(--c-text);
            font-family: var(--font-body);
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior: none;
        }

        /* ── Fondo con patrón sutil ──────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(245,166,35,0.12) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(245,166,35,0.06) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* ── Logo / Marca ────────────────────────────────── */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--c-accent), #c4841a);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 32px rgba(245,166,35,0.35);
        }
        .brand-name {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--c-text);
            letter-spacing: 0.03em;
            line-height: 1;
        }
        .brand-sub {
            font-size: 0.85rem;
            color: var(--c-muted);
            margin-top: 0.3rem;
        }

        /* ── Card del formulario ─────────────────────────── */
        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--c-surface);
            border: 1.5px solid var(--c-border);
            border-radius: 20px;
            padding: 2rem 1.75rem;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
        }

        .login-card-title {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--c-text);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* ── Campos ──────────────────────────────────────── */
        .field { margin-bottom: 1rem; }
        .field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--c-muted);
            margin-bottom: 0.4rem;
        }

        .field-wrap {
            position: relative;
        }
        .field-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--c-muted);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }
        .field-wrap:focus-within .field-icon { color: var(--c-accent); }

        .field-input {
            width: 100%;
            background: var(--c-bg);
            border: 1.5px solid var(--c-border);
            border-radius: 12px;
            color: var(--c-text);
            font-family: var(--font-body);
            font-size: 1rem;
            padding: 0.75rem 0.9rem 0.75rem 2.7rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
        }
        .field-input:focus {
            border-color: var(--c-accent);
            box-shadow: 0 0 0 3px rgba(245,166,35,0.15);
        }
        .field-input::placeholder { color: var(--c-muted); }

        /* Toggle contraseña */
        .pwd-toggle {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--c-muted);
            cursor: pointer;
            font-size: 1.05rem;
            padding: 0.2rem;
            line-height: 1;
            transition: color 0.15s;
        }
        .pwd-toggle:hover { color: var(--c-accent); }

        /* ── Error de validación ─────────────────────────── */
        .field-error {
            font-size: 0.78rem;
            color: #e84040;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .alert-error {
            background: rgba(232,64,64,0.08);
            border: 1.5px solid rgba(232,64,64,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: #e84040;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ── Botón submit ────────────────────────────────── */
        .btn-login {
            width: 100%;
            background: var(--c-accent);
            color: #111;
            border: none;
            border-radius: 12px;
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            padding: 0.9rem;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.15s, transform 0.1s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 16px rgba(245,166,35,0.3);
        }
        .btn-login:hover  { background: #c4841a; box-shadow: 0 6px 20px rgba(245,166,35,0.4); }
        .btn-login:active { transform: scale(0.97); }

        /* ── Banner de instalación PWA ───────────────────── */
        .pwa-banner {
            display: none;   /* se muestra via JS si el browser lo soporta */
            width: 100%;
            max-width: 400px;
            margin-top: 1rem;
            background: rgba(245,166,35,0.08);
            border: 1.5px solid rgba(245,166,35,0.3);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .pwa-banner:hover { background: rgba(245,166,35,0.14); }
        .pwa-banner-icon { font-size: 1.8rem; flex-shrink: 0; }
        .pwa-banner-text { flex: 1; }
        .pwa-banner-title { font-weight: 600; font-size: 0.9rem; color: var(--c-text); }
        .pwa-banner-sub   { font-size: 0.76rem; color: var(--c-muted); margin-top: 0.1rem; }
        .pwa-banner-btn   {
            background: var(--c-accent); color: #111;
            border: none; border-radius: 8px; font-weight: 700;
            font-size: 0.8rem; padding: 0.4rem 0.75rem; cursor: pointer; flex-shrink: 0;
        }

        /* ── iOS install hint ────────────────────────────── */
        .ios-hint {
            display: none;
            width: 100%;
            max-width: 400px;
            margin-top: 1rem;
            background: rgba(245,166,35,0.08);
            border: 1.5px solid rgba(245,166,35,0.3);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            font-size: 0.82rem;
            color: var(--c-muted);
            line-height: 1.5;
            text-align: center;
        }
        .ios-hint strong { color: var(--c-accent); }

        /* ── Footer ──────────────────────────────────────── */
        .login-footer {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--c-muted);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- Marca --}}
    <div class="brand">
        <div class="brand-icon">🛒</div>
        <div class="brand-name">{{ config('app.name') }}</div>
        <div class="brand-sub">Sistema de punto de venta</div>
    </div>

    {{-- Card login --}}
    <div class="login-card">
        <div class="login-card-title">Iniciar sesión</div>

        {{-- Error general --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label class="field-label" for="email">Correo electrónico</label>
                <div class="field-wrap">
                    <i class="bi bi-envelope field-icon"></i>
                    <input type="email" id="email" name="email"
                           class="field-input"
                           value="{{ old('email') }}"
                           placeholder="admin@minegocio.local"
                           autocomplete="email"
                           required autofocus>
                </div>
                @error('email')
                    <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="field">
                <label class="field-label" for="password">Contraseña</label>
                <div class="field-wrap">
                    <i class="bi bi-lock field-icon"></i>
                    <input type="password" id="password" name="password"
                           class="field-input"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="pwd-toggle" onclick="togglePwd()" id="pwdToggle"
                            tabindex="-1" title="Mostrar contraseña">
                        <i class="bi bi-eye" id="pwdIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Recordarme --}}
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                <input type="checkbox" name="remember" id="remember"
                       style="accent-color:var(--c-accent); width:16px; height:16px; cursor:pointer;">
                <label for="remember" style="font-size:0.85rem; color:var(--c-muted); cursor:pointer;">
                    Recordarme en este dispositivo
                </label>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <i class="bi bi-box-arrow-in-right"></i> ENTRAR
            </button>
        </form>
    </div>

    {{-- Banner instalación PWA — Android/Chrome --}}
    <div class="pwa-banner" id="pwaBanner">
        <div class="pwa-banner-icon">📲</div>
        <div class="pwa-banner-text">
            <div class="pwa-banner-title">Instalar en el celular</div>
            <div class="pwa-banner-sub">Acceso directo desde tu pantalla de inicio</div>
        </div>
        <button class="pwa-banner-btn" id="pwaBannerBtn">Instalar</button>
    </div>

    {{-- Hint iOS (Safari no soporta el evento beforeinstallprompt) --}}
    <div class="ios-hint" id="iosHint">
        📲 Para instalar en iPhone: tocá <strong>Compartir</strong>
        <i class="bi bi-box-arrow-up"></i> y luego
        <strong>"Agregar a inicio"</strong>
    </div>

    <div class="login-footer">
        {{ config('pos.business') }} · POS {{ config('pos.version') }} · {{ config('pos.location') }}
    </div>

</div>

<script>
// ── Toggle contraseña ──────────────────────────────────────────
function togglePwd() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pwdIcon');
    const shown = input.type === 'text';
    input.type  = shown ? 'password' : 'text';
    icon.className = shown ? 'bi bi-eye' : 'bi bi-eye-slash';
}

// ── Feedback en submit ─────────────────────────────────────────
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnLogin');
    btn.disabled = true;
    btn.innerHTML = '<span style="width:18px;height:18px;border:2px solid #111;border-top-color:transparent;border-radius:50%;display:inline-block;animation:spin 0.6s linear infinite;"></span> Entrando…';
});

// ── PWA: capturar evento de instalación (Android/Chrome) ───────
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    document.getElementById('pwaBanner').style.display = 'flex';
});

document.getElementById('pwaBannerBtn').addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    deferredPrompt = null;
    if (outcome === 'accepted') {
        document.getElementById('pwaBanner').style.display = 'none';
    }
});

// ── iOS: mostrar hint solo en Safari/iOS si no está instalada ──
const isIos     = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isSafari  = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
const isStandalone = window.navigator.standalone === true;

if (isIos && isSafari && !isStandalone) {
    document.getElementById('iosHint').style.display = 'block';
}

// Spinner CSS
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);

// ── Registrar Service Worker ───────────────────────────────────
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>

</body>
</html>
