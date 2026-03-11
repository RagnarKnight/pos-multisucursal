@extends('layouts.app')
@section('title', 'Usuarios')

@section('extra-css')
<style>
.page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem; }
.page-title  { font-family:var(--font-display);font-size:1.8rem;font-weight:800;color:var(--c-text); }
.page-title span { color:var(--c-accent); }

.tienda-section { margin-bottom:1.25rem; }
.tienda-section-label {
    font-size:0.72rem;font-weight:700;text-transform:uppercase;
    letter-spacing:0.08em;color:var(--c-muted);margin-bottom:.5rem;
    display:flex;align-items:center;gap:.4rem;
}

.user-card {
    background:var(--c-surface);border:1.5px solid var(--c-border);
    border-radius:14px;padding:.9rem 1.1rem;margin-bottom:.4rem;
    display:flex;align-items:center;gap:1rem;transition:border-color .15s;
}
.user-card:hover { border-color:var(--c-accent); }
.user-card.es-yo { border-left:4px solid var(--c-accent); }

.user-avatar {
    width:42px;height:42px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-family:var(--font-display);font-size:1rem;font-weight:800;flex-shrink:0;
}
.avatar-superadmin { background:rgba(155,89,182,.2);color:#9b59b6; }
.avatar-admin      { background:rgba(245,166,35,.15);color:var(--c-accent); }
.avatar-empleado   { background:var(--c-border);color:var(--c-muted); }

.user-info  { flex:1;min-width:0; }
.user-name  { font-weight:600;font-size:.95rem;color:var(--c-text); }
.user-email { font-size:.78rem;color:var(--c-muted); }

.rol-badge    { font-size:.68rem;font-weight:700;border-radius:6px;padding:.18rem .5rem;text-transform:uppercase;letter-spacing:.06em;flex-shrink:0; }
.rol-superadmin { background:rgba(155,89,182,.15);color:#9b59b6; }
.rol-admin      { background:rgba(245,166,35,.15);color:var(--c-accent); }
.rol-empleado   { background:var(--c-border);color:var(--c-muted); }
.yo-badge { font-size:.68rem;color:var(--c-muted);font-style:italic; }

.action-btn { background:none;border:1.5px solid var(--c-border);color:var(--c-muted);border-radius:8px;padding:.3rem .5rem;cursor:pointer;font-size:.88rem;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center; }
.action-btn:hover        { color:var(--c-accent);border-color:var(--c-accent); }
.action-btn.danger:hover { color:#e84040;border-color:#e84040; }

/* Toggle activo */
.btn-desactivar:hover { color:var(--c-accent);border-color:var(--c-accent); }
.btn-activar          { border-color:rgba(46,204,113,.4);color:#2ecc71; }
.btn-activar:hover    { background:rgba(46,204,113,.08); }

/* Card inactiva */
.user-card.inactivo { opacity:.65; border-style:dashed; }
.user-avatar.apagado { filter: grayscale(1); }
</style>
@endsection

@section('content')
@php $yo = auth()->user(); @endphp

<div class="page-header">
    <div class="page-title">👤 <span>Usuarios</span></div>
    <a href="{{ route('users.create') }}" class="btn-accent"
       style="font-size:.9rem;padding:.55rem 1rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border-radius:10px;">
        <i class="bi bi-plus-lg"></i> Nuevo usuario
    </a>
</div>

@if($yo->esSuperAdmin())
    {{-- Superadmin: ver agrupado por tienda --}}

    {{-- Primero los superadmins (sin tienda) --}}
    @php $superadmins = $users->where('rol', 'superadmin'); @endphp
    @if($superadmins->isNotEmpty())
    <div class="tienda-section">
        <div class="tienda-section-label">
            <i class="bi bi-shield-fill-check" style="color:#9b59b6;"></i> Super Administradores
        </div>
        @foreach($superadmins as $user)
            @include('users._card', ['user' => $user, 'yo' => $yo])
        @endforeach
    </div>
    @endif

    {{-- Luego por tienda --}}
    @foreach($tiendas as $tienda)
    @php $usuariosTienda = $users->where('tienda_id', $tienda->id)->where('rol','!=','superadmin'); @endphp
    @if($usuariosTienda->isNotEmpty())
    <div class="tienda-section">
        <div class="tienda-section-label">
            <i class="bi bi-shop" style="color:var(--c-accent);"></i>
            {{ $tienda->nombre }}
            @if($tienda->ciudad) — {{ $tienda->ciudad }} @endif
        </div>
        @foreach($usuariosTienda as $user)
            @include('users._card', ['user' => $user, 'yo' => $yo])
        @endforeach
    </div>
    @endif
    @endforeach

@else
    {{-- Admin: lista simple de su tienda --}}
    @foreach($users as $user)
        @include('users._card', ['user' => $user, 'yo' => $yo])
    @endforeach
@endif

@endsection
