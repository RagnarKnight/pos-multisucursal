@extends('layouts.app')
@section('title', 'Historial — Cierre de Caja')

@section('extra-css')
<style>
    .historial-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem; }
    .historial-title  { font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); }
    .historial-title span { color:var(--c-accent); }

    .fecha-picker input {
        background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:10px;
        color:var(--c-text); padding:0.6rem 0.9rem; font-size:0.95rem; outline:none; cursor:pointer;
    }
    .fecha-picker input:focus { border-color:var(--c-accent); }

    .resumen-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:0.6rem; margin-bottom:1.25rem; }
    @media(min-width:576px){ .resumen-grid{ grid-template-columns:repeat(4,1fr); } }

    .resumen-card { background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:12px; padding:0.85rem 1rem; text-align:center; }
    .resumen-card.highlight { border-color:var(--c-accent); background:linear-gradient(135deg,#1c1f21,rgba(245,166,35,0.07)); }
    .resumen-card-label  { font-size:0.7rem; color:var(--c-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.3rem; }
    .resumen-card-amount { font-family:var(--font-display); font-size:1.3rem; font-weight:800; }
    .color-efectivo { color:#2ecc71; }
    .color-transfer { color:#3498db; }
    .color-fiado    { color:#e84040; }
    .color-total    { color:var(--c-accent); }

    .order-row {
        background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:12px;
        padding:0.85rem 1rem; margin-bottom:0.5rem; display:flex; align-items:center; gap:0.75rem;
        transition:border-color 0.15s; cursor:pointer; text-decoration:none; color:inherit;
    }
    .order-row:hover { border-color:var(--c-accent); color:inherit; }
    .order-row.sin-caja-row { border-color:rgba(245,166,35,0.3); background:rgba(245,166,35,0.04); }

    .order-num   { font-family:var(--font-display); font-size:0.8rem; font-weight:700; color:var(--c-muted); min-width:36px; }
    .order-info  { flex:1; min-width:0; }
    .order-items-summary { font-size:0.82rem; color:var(--c-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .order-user  { font-size:0.75rem; color:var(--c-muted); }
    .order-total { font-family:var(--font-display); font-size:1.1rem; font-weight:800; color:var(--c-text); text-align:right; }
    .order-time  { font-size:0.75rem; color:var(--c-muted); }

    .badge-pago         { font-size:0.7rem; font-weight:700; border-radius:6px; padding:0.2rem 0.5rem; white-space:nowrap; }
    .badge-efectivo     { background:#1a4731; color:#2ecc71; }
    .badge-transferencia{ background:#1a2e47; color:#3498db; }
    .badge-fiado        { background:#3d1f1f; color:#e84040; }

    /* ── Sección sin caja ────────────────────────────────── */
    .sin-caja-banner {
        background: rgba(245,166,35,0.08);
        border: 1.5px solid rgba(245,166,35,0.4);
        border-radius: 14px;
        padding: 0.85rem 1.1rem;
        margin-bottom: 1rem;
    }
    .sin-caja-banner-title {
        display: flex; align-items: center; gap: 0.5rem;
        font-family: var(--font-display); font-size: 1rem; font-weight: 700;
        color: var(--c-accent); margin-bottom: 0.6rem; cursor: pointer;
        user-select: none;
    }
    .sin-caja-toggle { margin-left: auto; font-size: 0.9rem; transition: transform 0.25s; }
    .sin-caja-toggle.open { transform: rotate(180deg); }
    .sin-caja-body { display: none; }
    .sin-caja-body.open { display: block; }

    .section-label {
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.08em; color: var(--c-muted);
        margin: 0.75rem 0 0.5rem;
        display: flex; align-items: center; gap: 0.4rem;
    }

    .empty-state { text-align:center; padding:3rem; color:var(--c-muted); }
    .empty-state i { font-size:3rem; display:block; margin-bottom:1rem; }
</style>
@endsection

@section('content')

<div class="historial-header"style="margin-bottom:10px;">
    <div class="historial-title">🧾 Historial / <span>Caja</span></div>
    <form method="GET" action="{{ route('orders.index') }}" class="fecha-picker">
        <input type="date" name="fecha" value="{{ $fecha }}"
               max="{{ today()->toDateString() }}" onchange="this.form.submit()">
    </form>
</div>

{{-- Resumen del día --}}
<div class="resumen-grid">
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-cash-coin"></i> Efectivo</div>
        <div class="resumen-card-amount color-efectivo">${{ number_format($resumen['total_efectivo'], 0, ',', '.') }}</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-phone"></i> Transfer</div>
        <div class="resumen-card-amount color-transfer">${{ number_format($resumen['total_transferencia'], 0, ',', '.') }}</div>
    </div>
    <div class="resumen-card">
        <div class="resumen-card-label"><i class="bi bi-book"></i> Fiado</div>
        <div class="resumen-card-amount color-fiado">${{ number_format($resumen['total_fiado'], 0, ',', '.') }}</div>
    </div>
    <div class="resumen-card highlight">
        <div class="resumen-card-label">💰 COBRADO</div>
        <div class="resumen-card-amount color-total">${{ number_format($resumen['total_dia'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- ── Ventas SIN caja abierta ─────────────────────────────── --}}
@if($ordersSinCaja->isNotEmpty())
<div class="sin-caja-banner">
    <div class="sin-caja-banner-title" onclick="toggleSinCaja()">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ $ordersSinCaja->count() }} venta{{ $ordersSinCaja->count() > 1 ? 's' : '' }}
        registrada{{ $ordersSinCaja->count() > 1 ? 's' : '' }} SIN caja abierta
        <span style="font-size:0.8rem; font-weight:400; color:var(--c-muted); margin-left:0.25rem;">
            (${{ number_format($ordersSinCaja->sum('total'), 0, ',', '.') }} en total)
        </span>
        <i class="bi bi-chevron-down sin-caja-toggle" id="sinCajaToggle"></i>
    </div>
    <div class="sin-caja-body" id="sinCajaBody">
        <div style="font-size:0.8rem; color:var(--c-muted); margin-bottom:0.75rem; padding:0 0.1rem;">
            Estas ventas se hicieron sin que hubiera una caja abierta. Revisalas y abrí caja al inicio del turno.
        </div>
        @foreach($ordersSinCaja as $order)
        <a href="{{ route('orders.show', $order) }}" class="order-row sin-caja-row">
            <div class="order-num">#{{ $order->id }}</div>
            <div class="order-info">
                <div class="order-items-summary">
                    {{ $order->items->map(fn($i) => $i->cantidad.'x '.$i->product->nombre)->join(', ') }}
                </div>
                <div class="order-user">
                    {{ $order->user->name }}
                    @if($order->customer)· <span style="color:#e84040;">{{ $order->customer->nombre }}</span>@endif
                </div>
            </div>
            <div class="text-end">
                <div class="order-time">{{ $order->created_at->format('H:i') }}</div>
                <span class="badge-pago badge-{{ $order->metodo_pago }}">{{ strtoupper($order->metodo_pago) }}</span>
            </div>
            <div class="order-total">${{ number_format($order->total, 0, ',', '.') }}</div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── Ventas CON caja ──────────────────────────────────────── --}}
@if($orders->isNotEmpty())
    <div class="section-label">
        <i class="bi bi-check-circle-fill" style="color:#2ecc71;"></i>
        Ventas dentro de caja
    </div>
@endif

@forelse($orders as $order)
<a href="{{ route('orders.show', $order) }}" class="order-row">
    <div class="order-num">#{{ $order->id }}</div>
    <div class="order-info">
        <div class="order-items-summary">
            {{ $order->items->map(fn($i) => $i->cantidad.'x '.$i->product->nombre)->join(', ') }}
        </div>
        <div class="order-user">
            {{ $order->user->name }}
            @if($order->customer)· <span style="color:#e84040;">{{ $order->customer->nombre }}</span>@endif
        </div>
    </div>
    <div class="text-end">
        <div class="order-time">{{ $order->created_at->format('H:i') }}</div>
        <span class="badge-pago badge-{{ $order->metodo_pago }}">{{ strtoupper($order->metodo_pago) }}</span>
    </div>
    <div class="order-total">${{ number_format($order->total, 0, ',', '.') }}</div>
</a>
@empty
@if($ordersSinCaja->isEmpty())
<div class="empty-state">
    <i class="bi bi-receipt"></i>
    No hay ventas registradas para este día.
</div>
@endif
@endforelse

@endsection

@section('scripts')
<script>
function toggleSinCaja() {
    const body   = document.getElementById('sinCajaBody');
    const toggle = document.getElementById('sinCajaToggle');
    body.classList.toggle('open');
    toggle.classList.toggle('open');
}
</script>
@endsection
