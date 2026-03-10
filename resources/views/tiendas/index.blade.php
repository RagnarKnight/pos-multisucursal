@extends('layouts.app')
@section('title', 'Tiendas')

@section('extra-css')
<style>
    .page-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem; }
    .page-title  { font-family:var(--font-display);font-size:1.8rem;font-weight:800;color:var(--c-text); }
    .page-title span { color:var(--c-accent); }

    .tienda-card {
        background:var(--c-surface);border:1.5px solid var(--c-border);
        border-radius:16px;padding:1.25rem;margin-bottom:.75rem;
        display:flex;align-items:center;gap:1rem;
        transition:border-color .15s;
    }
    .tienda-card:hover { border-color:var(--c-accent); }
    .tienda-card.activa-now { border-left:4px solid var(--c-accent); }

    .tienda-logo {
        width:56px;height:56px;border-radius:12px;overflow:hidden;
        background:var(--c-border);display:flex;align-items:center;
        justify-content:center;font-size:1.6rem;flex-shrink:0;
    }
    .tienda-logo img { width:100%;height:100%;object-fit:contain;padding:4px; }

    .tienda-info  { flex:1;min-width:0; }
    .tienda-nombre { font-weight:700;font-size:1.05rem;color:var(--c-text); }
    .tienda-meta   { font-size:0.78rem;color:var(--c-muted);margin-top:.2rem; }

    .tienda-stats {
        display:flex;gap:1rem;
        font-size:0.78rem;color:var(--c-muted);
        flex-wrap:wrap;
    }
    .stat-pill {
        background:var(--c-border);border-radius:6px;
        padding:.2rem .55rem;display:flex;align-items:center;gap:.3rem;
    }
    .stat-pill span { color:var(--c-text);font-weight:600; }

    .action-btn { background:none;border:1.5px solid var(--c-border);color:var(--c-muted);border-radius:8px;padding:.35rem .55rem;cursor:pointer;font-size:.9rem;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center; }
    .action-btn:hover        { color:var(--c-accent);border-color:var(--c-accent); }
    .action-btn.danger:hover { color:#e84040;border-color:#e84040; }

    .badge-activa { background:rgba(46,204,113,.12);color:#2ecc71;border:1px solid rgba(46,204,113,.3);font-size:.7rem;font-weight:700;border-radius:6px;padding:.15rem .45rem;text-transform:uppercase;letter-spacing:.06em; }
</style>
@endsection

@section('content')

<div class="page-header">
    <div class="page-title">🏪 <span>Tiendas</span></div>
    @can('gestionar-tiendas')
    <a href="{{ route('tiendas.create') }}" class="btn-accent"
       style="font-size:.95rem;padding:.6rem 1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border-radius:10px;">
        <i class="bi bi-plus-lg"></i> Nueva tienda
    </a>
    @endcan
</div>

@php $tiendaActiva = auth()->user()->tiendaActiva(); @endphp

@foreach($tiendas as $t)
<div class="tienda-card {{ $tiendaActiva?->id == $t->id ? 'activa-now' : '' }}">

    <div class="tienda-logo">
        @if($t->logoUrl())
            <img src="{{ $t->logoUrl() }}" alt="{{ $t->nombre }}">
        @else
            🏪
        @endif
    </div>

    <div class="tienda-info">
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <div class="tienda-nombre">{{ $t->nombre }}</div>
            @if($tiendaActiva?->id == $t->id)
                <span class="badge-activa">activa</span>
            @endif
        </div>
        <div class="tienda-meta">
            @if($t->ciudad) <i class="bi bi-geo-alt"></i> {{ $t->ciudad }} @endif
            @if($t->direccion) · {{ $t->direccion }} @endif
            @if($t->telefono) · <i class="bi bi-telephone"></i> {{ $t->telefono }} @endif
        </div>
        <div class="tienda-stats" style="margin-top:.5rem;">
            <div class="stat-pill"><i class="bi bi-box-seam"></i> <span>{{ $t->products_count }}</span> productos</div>
            <div class="stat-pill"><i class="bi bi-people"></i> <span>{{ $t->users_count }}</span> usuarios</div>
            <div class="stat-pill"><i class="bi bi-receipt"></i> <span>{{ $t->orders_count }}</span> órdenes</div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:.35rem;align-items:flex-end;">

        {{-- Cambiar a esta tienda --}}
        @if($tiendaActiva?->id != $t->id)
        <form method="POST" action="{{ route('tienda.switch') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="tienda_id" value="{{ $t->id }}">
            <button type="submit" class="action-btn" title="Cambiar a esta tienda"
                    style="font-size:.78rem;gap:.3rem;">
                <i class="bi bi-arrow-left-right"></i> Cambiar
            </button>
        </form>
        @endif

        <a href="{{ route('tiendas.edit', $t) }}" class="action-btn" title="Configurar">
            <i class="bi bi-gear"></i>
        </a>

        @can('gestionar-tiendas')
        @if($tiendas->count() > 1)
        <form method="POST" action="{{ route('tiendas.destroy', $t) }}"
              onsubmit="return confirm('¿Eliminar {{ addslashes($t->nombre) }} y todos sus datos?')">
            @csrf @method('DELETE')
            <button type="submit" class="action-btn danger" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </form>
        @endif
        @endcan

    </div>
</div>
@endforeach

@endsection
