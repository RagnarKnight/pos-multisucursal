@extends('layouts.app')
@section('title', 'La Libreta — Clientes')

@section('extra-css')
<style>
    .libreta-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .libreta-title {
        font-family: var(--font-display);
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--c-text);
        letter-spacing: 0.03em;
    }
    .libreta-title span { color: var(--c-accent); }

    /* ── Resumen deuda total ─────────────────────────────── */
    .deuda-total-card {
        background: linear-gradient(135deg, #1c1f21 60%, rgba(245,166,35,0.08));
        border: 1.5px solid var(--c-accent);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .deuda-total-label { font-size: 0.85rem; color: var(--c-muted); font-weight: 500; }
    .deuda-total-amount {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 800;
        color: var(--c-accent);
    }

    /* ── Tarjetas de cliente ─────────────────────────────── */
    .customer-card {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        margin-bottom: 0.3rem;   /* reducido: el panel historial es el 0.3rem extra */
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: border-color 0.15s;
        text-decoration: none;
        color: inherit;
    }
    .customer-card:hover { border-color: var(--c-accent); color: inherit; }
    .customer-card.deudor  { border-left: 4px solid #e84040; }
    .customer-card.saldado { border-left: 4px solid #2ecc71; }

    .customer-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: var(--c-border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--c-accent);
        flex-shrink: 0;
    }

    .customer-info { flex: 1; min-width: 0; }
    .customer-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--c-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .customer-phone { font-size: 0.8rem; color: var(--c-muted); }

    .customer-saldo { text-align: right; flex-shrink: 0; }
    .saldo-amount { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; }
    .saldo-amount.deudor  { color: #e84040; }
    .saldo-amount.saldado { color: #2ecc71; }
    .saldo-label { font-size: 0.7rem; color: var(--c-muted); }

    /* ── Panel historial expandible ─────────────────────── */
    .customer-history-panel {
        display: none;
        margin-bottom: 0.6rem;
        background: var(--c-bg);
        border: 1px solid var(--c-border);
        border-radius: 0 0 12px 12px;
        border-top: none;
        overflow: hidden;
        animation: slideDown 0.2s ease;
    }
    .customer-history-panel.open { display: block; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .hist-mini-row {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.55rem 0.9rem; border-bottom: 1px solid var(--c-border);
        text-decoration: none; color: inherit; font-size: 0.82rem;
        transition: background 0.15s;
    }
    .hist-mini-row:last-of-type { border-bottom: none; }
    .hist-mini-row:hover { background: rgba(245,166,35,0.05); }
    .hist-mini-date  { color: var(--c-muted); min-width: 72px; font-size: 0.76rem; }
    .hist-mini-items { flex: 1; color: var(--c-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hist-mini-badge { font-size: 0.65rem; font-weight: 700; border-radius: 5px; padding: 0.15rem 0.4rem; flex-shrink: 0; }
    .hist-mini-total { font-family: var(--font-display); font-weight: 700; color: var(--c-text); min-width: 70px; text-align: right; }

    /* ── Modal saldar ────────────────────────────────────── */
    .modal-content {
        background: var(--c-surface) !important;
        border: 1.5px solid var(--c-border) !important;
        border-radius: 16px !important;
        color: var(--c-text) !important;
    }
    .modal-header { border-bottom-color: var(--c-border) !important; }
    .modal-footer { border-top-color: var(--c-border) !important; }
    .modal-title { font-family: var(--font-display); font-size: 1.3rem; font-weight: 700; }

    .form-control-dark {
        background: var(--c-bg) !important;
        border: 1.5px solid var(--c-border) !important;
        border-radius: 10px !important;
        color: var(--c-text) !important;
        font-size: 1.2rem !important;
        padding: 0.75rem !important;
    }
    .form-control-dark:focus {
        border-color: var(--c-accent) !important;
        box-shadow: 0 0 0 3px rgba(245,166,35,0.15) !important;
    }
    .btn-saldar-max {
        background: none; border: 1px solid var(--c-accent); color: var(--c-accent);
        border-radius: 8px; font-size: 0.8rem; padding: 0.3rem 0.75rem;
        cursor: pointer; margin-top: 0.4rem;
    }
</style>
@endsection

@section('content')

<div class="libreta-header" style="margin-bottom:10px;">
    <div class="libreta-title">📖 La <span>Libreta</span></div>
    <a href="{{ route('customers.create') }}" class="btn-accent"
       style="font-size:0.95rem; padding:0.6rem 1.1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; border-radius:10px;">
        <i class="bi bi-plus-lg"></i> Nuevo cliente
    </a>
</div>

{{-- Resumen total adeudado --}}
@php
    $totalDeuda   = $customers->sum('saldo_deudor');
    $cantDeudores = $customers->where('saldo_deudor', '>', 0)->count();
@endphp

<div class="deuda-total-card">
    <div>
        <div class="deuda-total-label">TOTAL ADEUDADO</div>
        <div class="deuda-total-amount">${{ number_format($totalDeuda, 0, ',', '.') }}</div>
        <div style="font-size:0.8rem; color:var(--c-muted); margin-top:0.2rem;">
            {{ $cantDeudores }} {{ $cantDeudores == 1 ? 'cliente debe' : 'clientes deben' }}
        </div>
    </div>
    <i class="bi bi-wallet2" style="font-size:2.5rem; color:rgba(245,166,35,0.3);"></i>
</div>

{{-- Lista de clientes --}}
@forelse($customers as $customer)

    {{-- La card entera es un <a> → navega al perfil del cliente --}}
    <a href="{{ route('customers.show', $customer) }}"
       class="customer-card {{ $customer->debeAlgo() ? 'deudor' : 'saldado' }}">

        <div class="customer-avatar">
            {{ strtoupper(substr($customer->nombre, 0, 2)) }}
        </div>

        <div class="customer-info">
            <div class="customer-name">{{ $customer->nombre }}</div>
            <div class="customer-phone">
                @if($customer->telefono)
                    <i class="bi bi-telephone"></i> {{ $customer->telefono }}
                @else
                    <i class="bi bi-telephone text-muted"></i>
                    <span style="color:var(--c-muted)">Sin teléfono</span>
                @endif
            </div>
        </div>

        <div class="customer-saldo">
            <div class="saldo-amount {{ $customer->debeAlgo() ? 'deudor' : 'saldado' }}">
                ${{ number_format($customer->saldo_deudor, 0, ',', '.') }}
            </div>
            <div class="saldo-label">{{ $customer->debeAlgo() ? 'DEBE' : 'AL DÍA' }}</div>
        </div>

        {{-- Botones: stopPropagation para que no naveguen al perfil --}}
        <div style="display:flex; flex-direction:column; gap:0.35rem; flex-shrink:0;"
             onclick="event.preventDefault(); event.stopPropagation();">

            {{-- Saldar: solo si debe --}}
            @if($customer->debeAlgo())
            <button class="btn-accent"
                    style="font-size:0.85rem; padding:0.4rem 0.9rem; border-radius:10px; white-space:nowrap;"
                    onclick="abrirSaldar({{ $customer->id }}, '{{ addslashes($customer->nombre) }}', {{ $customer->saldo_deudor }})">
                <i class="bi bi-cash"></i> Saldar
            </button>
            @endif

        </div>
    </a>

    {{-- Panel historial expandible (fuera del <a> para evitar links anidados) --}}
    @if($customer->orders->isNotEmpty())
    <div class="customer-history-panel" id="hist-{{ $customer->id }}">
        @foreach($customer->orders->sortByDesc('created_at')->take(8) as $order)
        <a href="{{ route('orders.show', $order) }}" class="hist-mini-row">
            <span class="hist-mini-date">{{ $order->created_at->format('d/m H:i') }}</span>
            <span class="hist-mini-items">
                {{ $order->items->map(fn($i) => $i->cantidad.'x '.$i->product->nombre)->join(', ') }}
            </span>
            <span class="hist-mini-badge badge-{{ $order->metodo_pago }}">
                {{ strtoupper($order->metodo_pago) }}
            </span>
            <span class="hist-mini-total">${{ number_format($order->total, 0, ',', '.') }}</span>
        </a>
        @endforeach

        @if($customer->orders->count() > 8)
        <a href="{{ route('customers.show', $customer) }}"
           style="display:block; text-align:center; padding:0.5rem; font-size:0.78rem; color:var(--c-accent); text-decoration:none; border-top:1px solid var(--c-border);">
            Ver historial completo ({{ $customer->orders->count() }} compras) →
        </a>
        @endif
    </div>
    @endif

@empty
    <div style="text-align:center; padding:3rem; color:var(--c-muted);">
        <i class="bi bi-people" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
        Todavía no hay clientes en la libreta.
    </div>
@endforelse

{{-- Modal Saldar cuenta --}}
<div class="modal fade" id="modalSaldar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cash-coin text-warning me-2"></i> Saldar cuenta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSaldar" method="POST">
                @csrf
                <div class="modal-body">
                    <p style="color:var(--c-muted); margin-bottom:1rem;" id="modalClienteNombre"></p>
                    <label style="font-size:0.85rem; color:var(--c-muted); margin-bottom:0.4rem; display:block;">
                        MONTO A PAGAR
                    </label>
                    <input type="number" name="monto" id="montoInput"
                           class="form-control form-control-dark"
                           step="0.01" min="0.01" required>
                    <button type="button" class="btn-saldar-max" onclick="pagarTodo()">
                        Pagar todo ($<span id="totalDeudaModal">0</span>)
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-accent" style="border-radius:10px; padding:0.6rem 1.25rem;">
                        <i class="bi bi-check-lg"></i> Confirmar pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let deudaActual = 0;


function abrirSaldar(customerId, nombre, deuda) {
    deudaActual = deuda;
    document.getElementById('modalClienteNombre').textContent = nombre + ' debe $' + fmt(deuda);
    document.getElementById('totalDeudaModal').textContent    = fmt(deuda);
    document.getElementById('montoInput').value  = '';
    document.getElementById('montoInput').max    = deuda;
    document.getElementById('formSaldar').action = `/customers/${customerId}/saldar`;
    new bootstrap.Modal(document.getElementById('modalSaldar')).show();
}

function pagarTodo() {
    document.getElementById('montoInput').value = deudaActual;
}

function fmt(n) {
    return new Intl.NumberFormat('es-AR', { minimumFractionDigits:0, maximumFractionDigits:2 }).format(n);
}
</script>
@endsection
