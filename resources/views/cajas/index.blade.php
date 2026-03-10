@extends('layouts.app')
@section('title', 'Control de Caja')

@section('extra-css')
<style>
    .page-title { font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-bottom:1.25rem; }
    .page-title span { color:var(--c-accent); }

    /* ── Estado de caja ──────────────────────────────────── */
    .caja-status-card {
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .caja-status-card.abierta {
        background: linear-gradient(135deg, #1a4731, rgba(46,204,113,0.08));
        border: 1.5px solid rgba(46,204,113,0.4);
    }
    .caja-status-card.cerrada {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
    }

    .caja-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        animation: pulse 2s infinite;
    }
    .caja-dot.verde { background: #2ecc71; }
    .caja-dot.gris  { background: var(--c-muted); animation: none; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.4; }
    }

    .caja-status-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--c-muted); margin-bottom: 0.4rem; }
    .caja-monto-apertura { font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #2ecc71; }
    .caja-meta { font-size: 0.82rem; color: var(--c-muted); margin-top: 0.3rem; }

    /* ── KPIs del turno actual ───────────────────────────── */
    .turno-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.6rem;
        margin: 1rem 0;
    }
    .turno-card { background: rgba(0,0,0,0.2); border-radius: 10px; padding: 0.75rem; text-align: center; }
    .turno-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--c-muted); margin-bottom: 0.25rem; }
    .turno-val   { font-family: var(--font-display); font-size: 1rem; font-weight: 700; }

    /* ── Formularios ─────────────────────────────────────── */
    .form-row { display: flex; gap: 0.6rem; align-items: flex-end; flex-wrap: wrap; margin-top: 1rem; }
    .form-field { flex: 1; min-width: 140px; }
    .field-label { font-size: 0.78rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.3rem; display: block; }
    .field-input {
        width: 100%;
        background: rgba(0,0,0,0.3);
        border: 1.5px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: var(--c-text);
        font-family: var(--font-display);
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.65rem 0.9rem;
        outline: none;
    }
    .field-input:focus { border-color: var(--c-accent); }

    /* ── Diferencia en cierre ────────────────────────────── */
    .diferencia-live {
        margin-top: 0.5rem;
        font-family: var(--font-display);
        font-size: 0.9rem;
        font-weight: 700;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        display: inline-block;
    }
    .dif-ok    { background: rgba(46,204,113,0.15); color: #2ecc71; }
    .dif-sobre { background: rgba(52,152,219,0.15); color: #3498db; }
    .dif-falta { background: rgba(232,64,64,0.15);  color: #e84040; }

    /* ── Historial --*/
    .historial-card { background: var(--c-surface); border: 1.5px solid var(--c-border); border-radius: 14px; overflow: hidden; }
    .historial-header { padding: 0.75rem 1.1rem; border-bottom: 1px solid var(--c-border); font-size: 0.75rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: 0.08em; }

    .hist-row { display: grid; grid-template-columns: 90px 70px 75px 1fr 80px 65px; align-items: center; padding: 0.65rem 1.1rem; border-bottom: 1px solid var(--c-border); gap: 0.4rem; font-size: 0.82rem; }
    .hist-row:last-child { border-bottom: none; }
    .hist-fecha { color: var(--c-muted); font-size: 0.78rem; }
    .hist-user  { color: var(--c-muted); font-size: 0.78rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .hist-ap    { color: var(--c-muted); }
    .hist-ventas { font-size: 0.78rem; }
    .hist-real  { color: var(--c-text); font-family: var(--font-display); font-weight: 700; }
    .hist-dif   { text-align: right; font-family: var(--font-display); font-weight: 700; font-size: 0.82rem; }
    .hist-notas { grid-column: 1 / -1; font-size: 0.75rem; color: var(--c-muted); font-style: italic; padding: 0 0 0.3rem; display:none; }
    .hist-row:hover .hist-notas { display: block; }
</style>
@endsection

@section('content')

<div class="page-title">🏧 Control de <span>Caja</span></div>

@if($cajaAbierta)
{{-- ── CAJA ABIERTA ─────────────────────────────────────────── --}}
@php
    // Ventas desde apertura en tiempo real
    $ventasTurno = \App\Models\Order::where('created_at', '>=', $cajaAbierta->abierta_at)
        ->selectRaw("
            SUM(CASE WHEN metodo_pago='efectivo'      THEN total ELSE 0 END) as ef,
            SUM(CASE WHEN metodo_pago='transferencia' THEN total ELSE 0 END) as tr,
            SUM(CASE WHEN metodo_pago='fiado'         THEN total ELSE 0 END) as fi,
            COUNT(*) as cant
        ")->first();
    $esperadoEnCaja = $cajaAbierta->monto_apertura + ($ventasTurno->ef ?? 0);
@endphp

<div class="caja-status-card abierta">
    <div class="caja-status-label">
        <span class="caja-dot verde"></span> Caja abierta — turno activo
    </div>
    <div class="caja-monto-apertura">${{ number_format($cajaAbierta->monto_apertura, 0, ',', '.') }}</div>
    <div class="caja-meta">
        Apertura: {{ $cajaAbierta->abierta_at->format('d/m/Y H:i') }}
        · {{ $cajaAbierta->user->name }}
    </div>

    {{-- KPIs del turno --}}
    <div class="turno-grid">
        <div class="turno-card">
            <div class="turno-label">Efectivo vendido</div>
            <div class="turno-val" style="color:#2ecc71;">${{ number_format($ventasTurno->ef, 0, ',', '.') }}</div>
        </div>
        <div class="turno-card">
            <div class="turno-label">Transfer</div>
            <div class="turno-val" style="color:#3498db;">${{ number_format($ventasTurno->tr, 0, ',', '.') }}</div>
        </div>
        <div class="turno-card">
            <div class="turno-label">En caja ahora</div>
            <div class="turno-val" style="color:var(--c-accent);">${{ number_format($esperadoEnCaja, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Formulario de cierre --}}
    <form method="POST" action="{{ route('cajas.cerrar', $cajaAbierta) }}">
        @csrf
        <div class="form-row">
            <div class="form-field">
                <label class="field-label">Efectivo real en caja al cerrar</label>
                <input type="number" name="monto_cierre" id="montoCierre"
                       class="field-input" step="0.01" min="0" required
                       placeholder="$0" oninput="calcDif({{ $esperadoEnCaja }})">
                <span class="diferencia-live" id="difLabel" style="display:none;"></span>
            </div>
            <div class="form-field">
                <label class="field-label">Notas del cierre (opcional)</label>
                <input type="text" name="notas_cierre" class="field-input"
                       placeholder="Observaciones…" style="font-size:0.9rem; font-weight:400;">
            </div>
            <button type="submit" class="btn-accent"
                    style="border-radius:10px; padding:0.65rem 1.25rem; white-space:nowrap; align-self:flex-end;"
                    onclick="return confirm('¿Confirmar cierre de caja?')">
                <i class="bi bi-lock-fill"></i> Cerrar caja
            </button>
        </div>
    </form>
</div>

@else
{{-- ── CAJA CERRADA — Formulario apertura ──────────────────── --}}
<div class="caja-status-card cerrada">
    <div class="caja-status-label">
        <span class="caja-dot gris"></span> Sin caja abierta
    </div>
    <div style="font-family:var(--font-display); font-size:1.3rem; font-weight:700; color:var(--c-text); margin-bottom:0.5rem;">
        Abrí la caja para empezar el turno
    </div>
    <div style="font-size:0.85rem; color:var(--c-muted); margin-bottom:1rem;">
        Ingresá el efectivo que hay en la caja al inicio del día.
    </div>

    <form method="POST" action="{{ route('cajas.abrir') }}">
        @csrf
        <div class="form-row">
            <div class="form-field">
                <label class="field-label">Monto inicial en efectivo</label>
                <input type="number" name="monto_apertura" class="field-input"
                       step="0.01" min="0" required placeholder="$0" autofocus>
            </div>
            <button type="submit" class="btn-accent"
                    style="border-radius:10px; padding:0.65rem 1.5rem; align-self:flex-end; white-space:nowrap;">
                <i class="bi bi-unlock-fill"></i> Abrir caja
            </button>
        </div>
    </form>
</div>
@endif

{{-- ── Historial de cajas ───────────────────────────────────── --}}
<div class="historial-card text-center">
    <div class="historial-header">
        <i class="bi bi-clock-history"></i> Historial de cierres (últimos 30)
    </div>
    @forelse($historial as $c)
    @php
        $dif = $c->diferencia();
        $difClass = $dif == 0 ? 'dif-ok' : ($dif > 0 ? 'dif-sobre' : 'dif-falta');
        $difPrefix = $dif > 0 ? '+' : ($dif < 0 ? '-' : '');
        $saldoReal = $c->monto_cierre ?? '—';
    @endphp
    <div class="hist-row" title="{{ $c->notas_cierre ?? '' }}">
        <div class="hist-fecha">{{ $c->cerrada_at->format('d/m H:i') }}</div>
        <div class="hist-user">{{ $c->user->name }}</div>
        <div class="hist-ap" style="color:var(--c-muted);">$\{{ number_format($c->monto_apertura, 0, ',', '.') }}</div>
        <div class="hist-ventas text-center">
            <span style="color:#2ecc71;">Esperado ${{ number_format($c->total_efectivo, 0, ',', '.') }}</span>
            <span style="color:var(--c-muted);"></span>
        </div>
        <div class="hist-real text-center">
            <div class="hist-real" style="display:flex; flex-direction:column;">
                <span>Real</span>
                @if($c->monto_cierre !== null)
                    ${{ number_format($c->monto_cierre, 0, ',', '.') }}
                @else
                    <span style="color:var(--c-muted);">—</span>
                @endif
            </div>
        </div>
        {{--<div class="hist-dif {{ $difClass }}">{{ $difPrefix }}${{ number_format(abs($dif), 0, ',', '.') }}</div>--}}
        @if($c->notas_cierre)
        <div class="hist-notas">💬 {{ $c->notas_cierre }}</div>
        @endif
    </div>
    @empty
    <div style="text-align:center; padding:2rem; color:var(--c-muted); font-size:0.9rem;">
        <i class="bi bi-clock" style="font-size:1.8rem; display:block; margin-bottom:0.5rem;"></i>
        Todavía no hay cierres registrados.
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<script>
function calcDif(esperado) {
    const real = parseFloat(document.getElementById('montoCierre').value) || 0;
    const dif  = real - esperado;
    const el   = document.getElementById('difLabel');
    el.style.display = 'inline-block';

    if (dif === 0) {
        el.className = 'diferencia-live dif-ok';
        el.textContent = '✓ Sin diferencias';
    } else if (dif > 0) {
        el.className = 'diferencia-live dif-sobre';
        el.textContent = `▲ Sobraron $${fmt(Math.abs(dif))}`;
    } else {
        el.className = 'diferencia-live dif-falta';
        el.textContent = `▼ Faltan $${fmt(Math.abs(dif))}`;
    }
}

function fmt(n) {
    return new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
}
</script>
@endsection
