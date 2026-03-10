@extends('layouts.app')
@section('title', $customer->nombre . ' — Libreta')

@section('extra-css')
<style>
    .customer-detail-wrapper { max-width: 560px; margin: 0 auto; }

    .customer-hero {
        background: var(--c-surface);
        border: 1.5px solid {{ $customer->debeAlgo() ? 'rgba(232,64,64,0.4)' : 'rgba(46,204,113,0.3)' }};
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .customer-avatar-big {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: var(--c-border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--c-accent);
        flex-shrink: 0;
    }

    .c-name { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--c-text); }
    .c-phone { font-size: 0.85rem; color: var(--c-muted); margin-top: 0.15rem; }

    .saldo-big {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-left: auto;
        text-align: right;
    }
    .saldo-big.deudor  { color: #e84040; }
    .saldo-big.saldado { color: #2ecc71; }
    .saldo-label { font-size: 0.72rem; color: var(--c-muted); text-align: right; }

    /* Saldar --*/
    .saldar-card {
        background: rgba(232,64,64,0.05);
        border: 1.5px solid rgba(232,64,64,0.3);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }
    .saldar-title {
        font-size: 0.78rem; font-weight: 700; color: #e84040;
        text-transform: uppercase; letter-spacing: 0.08em;
        margin-bottom: 0.75rem;
    }
    .saldar-row { display: flex; gap: 0.5rem; align-items: stretch; }
    .saldar-input {
        flex: 1;
        background: var(--c-bg);
        border: 1.5px solid rgba(232,64,64,0.4);
        border-radius: 10px;
        color: var(--c-text);
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.65rem 0.9rem;
        outline: none;
    }
    .saldar-input:focus { border-color: #e84040; }
    .btn-saldar {
        background: #e84040;
        border: none;
        border-radius: 10px;
        color: #fff;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        padding: 0.65rem 1.1rem;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s;
    }
    .btn-saldar:hover { background: #c43030; }
    .btn-todo {
        background: none;
        border: 1px solid rgba(232,64,64,0.4);
        color: #e84040;
        border-radius: 7px;
        font-size: 0.78rem;
        padding: 0.25rem 0.6rem;
        cursor: pointer;
        margin-top: 0.4rem;
    }

    /* Historial de órdenes */
    .history-card {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 14px;
        overflow: hidden;
    }
    .history-header {
        padding: 0.75rem 1.1rem;
        border-bottom: 1px solid var(--c-border);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--c-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .history-row {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.1rem;
        border-bottom: 1px solid var(--c-border);
        gap: 0.75rem;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }
    .history-row:last-child { border-bottom: none; }
    .history-row:hover { background: rgba(245,166,35,0.04); }

    .h-date { font-size: 0.78rem; color: var(--c-muted); min-width: 90px; }
    .h-items { flex: 1; font-size: 0.82rem; color: var(--c-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .h-total { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--c-text); }
    .h-badge {
        font-size: 0.68rem; font-weight: 700;
        border-radius: 5px; padding: 0.15rem 0.4rem;
    }
    .badge-efectivo      { background:#1a4731; color:#2ecc71; }
    .badge-transferencia { background:#1a2e47; color:#3498db; }
    .badge-fiado         { background:#3d1f1f; color:#e84040; }

    .empty-history { text-align:center; padding:2rem; color:var(--c-muted); font-size:0.9rem; }
</style>
@endsection

@section('content')
<div class="customer-detail-wrapper">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:1rem;">
        <a href="{{ route('customers.index') }}"
           style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> La Libreta
        </a>
    </div>

    {{-- Hero --}}
    <div class="customer-hero">
        <div class="customer-avatar-big">
            {{ strtoupper(substr($customer->nombre, 0, 2)) }}
        </div>
        <div>
            <div class="c-name">{{ $customer->nombre }}</div>
            <div class="c-phone">
                @if($customer->telefono)
                    <i class="bi bi-telephone"></i> {{ $customer->telefono }}
                @else
                    Sin teléfono
                @endif
            </div>
        </div>
        <div>
            <div class="saldo-big {{ $customer->debeAlgo() ? 'deudor' : 'saldado' }}">
                ${{ number_format($customer->saldo_deudor, 0, ',', '.') }}
            </div>
            <div class="saldo-label">{{ $customer->debeAlgo() ? 'DEBE' : 'AL DÍA ✓' }}</div>
        </div>
    </div>

    {{-- Panel de saldar (solo si debe) --}}
    @if($customer->debeAlgo())
    <div class="saldar-card">
        <div class="saldar-title"><i class="bi bi-cash-coin"></i> Registrar pago</div>
        <form method="POST" action="{{ route('customers.saldar', $customer) }}">
            @csrf
            <div class="saldar-row">
                <input type="number" name="monto" id="montoInput" class="saldar-input"
                       placeholder="$0" step="0.01" min="0.01"
                       max="{{ $customer->saldo_deudor }}" required>
                <button type="submit" class="btn-saldar">
                    <i class="bi bi-check-lg"></i> Cobrar
                </button>
            </div>
            <button type="button" class="btn-todo"
                    onclick="document.getElementById('montoInput').value={{ $customer->saldo_deudor }}">
                Pagar todo (${{ number_format($customer->saldo_deudor, 0, ',', '.') }})
            </button>
        </form>
    </div>
    @endif

    {{-- Historial de compras del cliente --}}
    <div class="history-card">
        <div class="history-header">
            <i class="bi bi-clock-history"></i>
            Historial de compras ({{ $customer->orders->count() }})
        </div>

        @forelse($customer->orders->sortByDesc('created_at') as $order)
        <a href="{{ route('orders.show', $order) }}" class="history-row">
            <div class="h-date">
                {{ $order->created_at->format('d/m H:i') }}
            </div>
            <div class="h-items">
                {{ $order->items->map(fn($i) => $i->cantidad.'x '.$i->product->nombre)->join(', ') }}
            </div>
            <span class="h-badge badge-{{ $order->metodo_pago }}">
                {{ strtoupper($order->metodo_pago) }}
            </span>
            <div class="h-total">${{ number_format($order->total, 0, ',', '.') }}</div>
        </a>
        @empty
        <div class="empty-history">
            <i class="bi bi-receipt" style="font-size:1.8rem; display:block; margin-bottom:0.5rem;"></i>
            Sin compras registradas todavía.
        </div>
        @endforelse
    </div>

    {{-- Acciones --}}
    <div style="display:flex; gap:0.6rem; margin-top:1rem; flex-wrap:wrap;">
        <a href="{{ route('customers.edit', $customer) }}" class="btn-outline-ghost"
           style="display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none;">
            <i class="bi bi-pencil"></i> Editar cliente
        </a>
    </div>

</div>
@endsection
