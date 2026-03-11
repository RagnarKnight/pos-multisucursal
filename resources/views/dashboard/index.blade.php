@extends('layouts.app')
@section('title', 'Dashboard — Super Admin')

@section('extra-css')
<style>
/* ── Layout ───────────────────────────────────────────────── */
.dash-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem;
}
.dash-title {
    font-family: var(--font-display); font-size: 1.8rem;
    font-weight: 800; color: var(--c-text);
}
.dash-title span { color: var(--c-accent); }
.dash-date { font-size: 0.82rem; color: var(--c-muted); }

/* ── KPI consolidado ─────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.75rem; margin-bottom: 1.5rem;
}
.kpi-card {
    background: var(--c-surface); border: 1.5px solid var(--c-border);
    border-radius: 14px; padding: 1rem;
}
.kpi-card.accent { border-color: rgba(245,166,35,0.4); background: rgba(245,166,35,0.05); }
.kpi-card.danger { border-color: rgba(232,64,64,0.3);  background: rgba(232,64,64,0.04); }
.kpi-card.warn   { border-color: rgba(245,166,35,0.25); }
.kpi-label { font-size: 0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--c-muted); margin-bottom:0.3rem; }
.kpi-val   { font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--c-text); line-height: 1; }
.kpi-card.accent .kpi-val { color: var(--c-accent); }
.kpi-card.danger .kpi-val { color: #e84040; }

/* ── Tarjeta por tienda ──────────────────────────────────── */
.tienda-block {
    background: var(--c-surface); border: 1.5px solid var(--c-border);
    border-radius: 16px; margin-bottom: 1rem; overflow: hidden;
}
.tienda-block-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.1rem; border-bottom: 1px solid var(--c-border);
    gap: 0.75rem; flex-wrap: wrap;
}
.tienda-logo-sm {
    width: 38px; height: 38px; border-radius: 8px; overflow: hidden;
    background: var(--c-border); display: flex; align-items: center;
    justify-content: center; font-size: 1.2rem; flex-shrink: 0;
}
.tienda-logo-sm img { width:100%;height:100%;object-fit:contain;padding:2px; }
.tienda-nombre-h { font-family:var(--font-display); font-size:1.1rem; font-weight:700; color:var(--c-text); }
.tienda-ciudad-h { font-size:0.78rem; color:var(--c-muted); }

.caja-badge {
    font-size: 0.72rem; font-weight: 700; border-radius: 6px;
    padding: 0.2rem 0.55rem; text-transform: uppercase; letter-spacing: 0.06em;
}
.caja-abierta { background: rgba(46,204,113,0.12); color: #2ecc71; border: 1px solid rgba(46,204,113,0.3); }
.caja-cerrada { background: var(--c-border); color: var(--c-muted); }

.tienda-kpis {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0; border-bottom: 1px solid var(--c-border);
}
.t-kpi {
    padding: 0.85rem 1rem; border-right: 1px solid var(--c-border);
    text-align: center;
}
.t-kpi:last-child { border-right: none; }
.t-kpi-label { font-size: 0.68rem; color: var(--c-muted); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; }
.t-kpi-val   { font-family: var(--font-display); font-size: 1.25rem; font-weight: 800; color: var(--c-text); margin-top: 0.15rem; }
.t-kpi-val.green { color: #2ecc71; }
.t-kpi-val.red   { color: #e84040; }
.t-kpi-val.amber { color: var(--c-accent); }

.tienda-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.75rem 1.1rem; flex-wrap: wrap; gap: 0.5rem;
}
.deudores-list { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.deudor-chip {
    font-size: 0.72rem; background: rgba(232,64,64,0.08);
    border: 1px solid rgba(232,64,64,0.2); border-radius: 6px;
    color: var(--c-muted); padding: 0.2rem 0.5rem;
}
.deudor-chip strong { color: #e84040; }

.btn-ir-tienda {
    font-size: 0.8rem; background: none; border: 1.5px solid var(--c-border);
    color: var(--c-muted); border-radius: 8px; padding: 0.35rem 0.7rem;
    cursor: pointer; text-decoration: none; display: inline-flex;
    align-items: center; gap: 0.3rem; transition: all 0.15s;
}
.btn-ir-tienda:hover { border-color: var(--c-accent); color: var(--c-accent); }

/* ── Gráfico ─────────────────────────────────────────────── */
.chart-card {
    background: var(--c-surface); border: 1.5px solid var(--c-border);
    border-radius: 16px; padding: 1.25rem; margin-bottom: 1rem;
}
.chart-title {
    font-family: var(--font-display); font-size: 1rem; font-weight: 700;
    color: var(--c-text); margin-bottom: 1rem;
}

/* ── Superadmins ─────────────────────────────────────────── */
.sadmin-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
.sadmin-chip {
    display: flex; align-items: center; gap: 0.4rem;
    background: var(--c-border); border-radius: 8px; padding: 0.4rem 0.7rem;
    font-size: 0.82rem; color: var(--c-text);
}
.sadmin-chip .me { background: var(--c-accent); color: #111; border-radius: 4px; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.3rem; }
</style>
@endsection

@section('content')

{{-- Header ─────────────────────────────────────────────────────── --}}
<div class="dash-header">
    <div>
        <div class="dash-title">📊 Resumen del negocio <span>Administrador</span></div>
        <div class="dash-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        </div>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        @can('gestionar-tiendas')
        <a href="{{ route('tiendas.index') }}" class="btn-accent"
           style="text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border-radius:10px;padding:.55rem 1rem;font-size:.9rem;">
            <i class="bi bi-shop"></i> Tiendas
        </a>
        @endcan
        <a href="{{ route('users.index') }}"
           style="text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border-radius:10px;padding:.55rem 1rem;font-size:.9rem;background:var(--c-surface);border:1.5px solid var(--c-border);color:var(--c-text);">
            <i class="bi bi-people"></i> Usuarios
        </a>
    </div>
</div>

{{-- KPIs consolidados ───────────────────────────────────────────── --}}
<div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--c-muted);margin-bottom:.6rem;">
    ▸ Consolidado de hoy — todas las tiendas
</div>
<div class="kpi-grid">
    <div class="kpi-card accent">
        <div class="kpi-label">💰 Cobrado hoy</div>
        <div class="kpi-val">${{ number_format($consolidado['ventas_hoy'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">📋 Órdenes hoy</div>
        <div class="kpi-val">{{ $consolidado['ordenes_hoy'] }}</div>
    </div>
    <div class="kpi-card warn">
        <div class="kpi-label">📒 Fiado hoy</div>
        <div class="kpi-val">${{ number_format($consolidado['fiado_hoy'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card danger">
        <div class="kpi-label">💸 Deuda total</div>
        <div class="kpi-val">${{ number_format($consolidado['total_deuda'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card {{ $consolidado['sin_stock'] > 0 ? 'danger' : '' }}">
        <div class="kpi-label">📦 Sin stock</div>
        <div class="kpi-val">{{ $consolidado['sin_stock'] }}</div>
    </div>
</div>

{{-- Gráfico 7 días ──────────────────────────────────────────────── --}}
<div class="chart-card">
    <div class="chart-title">Ventas últimos 7 días por tienda</div>
    <canvas id="chartVentas" height="90"></canvas>
</div>

{{-- Bloque por tienda ───────────────────────────────────────────── --}}
<div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--c-muted);margin-bottom:.6rem;margin-top:1.25rem;">
    ▸ Estado actual por tienda
</div>

@foreach($metricasPorTienda as $m)
@php $t = $m['tienda']; @endphp
<div class="tienda-block">

    {{-- Header tienda --}}
    <div class="tienda-block-header">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <div class="tienda-logo-sm">
                @if($t->logoUrl()) <img src="{{ $t->logoUrl() }}" alt="{{ $t->nombre }}">
                @else 🏪 @endif
            </div>
            <div>
                <div class="tienda-nombre-h">{{ $t->nombre }}</div>
                @if($t->ciudad) <div class="tienda-ciudad-h"><i class="bi bi-geo-alt"></i> {{ $t->ciudad }}</div> @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="caja-badge {{ $m['caja_abierta'] ? 'caja-abierta' : 'caja-cerrada' }}">
                <i class="bi bi-{{ $m['caja_abierta'] ? 'unlock' : 'lock' }}"></i>
                Caja {{ $m['caja_abierta'] ? 'abierta' : 'cerrada' }}
            </span>
            {{-- Switch a esta tienda --}}
            <form method="POST" action="{{ route('tienda.switch') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="tienda_id" value="{{ $t->id }}">
                <button type="submit" class="btn-ir-tienda">
                    <i class="bi bi-arrow-right-circle"></i> Operar
                </button>
            </form>
        </div>
    </div>

    {{-- KPIs de la tienda --}}
    <div class="tienda-kpis">
        <div class="t-kpi">
            <div class="t-kpi-label">Cobrado hoy</div>
            <div class="t-kpi-val green">${{ number_format($m['ventas_hoy'], 0, ',', '.') }}</div>
        </div>
        <div class="t-kpi">
            <div class="t-kpi-label">Órdenes</div>
            <div class="t-kpi-val">{{ $m['ordenes_hoy'] }}</div>
        </div>
        <div class="t-kpi">
            <div class="t-kpi-label">Fiado hoy</div>
            <div class="t-kpi-val amber">${{ number_format($m['fiado_hoy'], 0, ',', '.') }}</div>
        </div>
        <div class="t-kpi">
            <div class="t-kpi-label">Deuda total</div>
            <div class="t-kpi-val red">${{ number_format($m['total_deuda'], 0, ',', '.') }}</div>
        </div>
        <div class="t-kpi">
            <div class="t-kpi-label">Usuarios</div>
            <div class="t-kpi-val">{{ $t->users_count }}</div>
        </div>
        <div class="t-kpi">
            <div class="t-kpi-label">Sin stock</div>
            <div class="t-kpi-val {{ $m['sin_stock'] > 0 ? 'red' : '' }}">
                {{ $m['sin_stock'] }}
                @if($m['stock_bajo'] > 0)
                    <span style="font-size:.65rem;color:var(--c-accent);">+{{ $m['stock_bajo'] }} bajo</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Deudores top + acceso rápido --}}
    <div class="tienda-footer">
        <div>
            @if($m['deudores_top']->isNotEmpty())
                <div style="font-size:.7rem;color:var(--c-muted);margin-bottom:.3rem;">Top deudores:</div>
                <div class="deudores-list">
                    @foreach($m['deudores_top'] as $d)
                    <span class="deudor-chip">
                        {{ $d->nombre }} — <strong>${{ number_format($d->saldo_deudor, 0, ',', '.') }}</strong>
                    </span>
                    @endforeach
                </div>
            @else
                <span style="font-size:.78rem;color:var(--c-muted);">Sin deudas pendientes ✅</span>
            @endif
        </div>
        <div>
            <a href="{{ route('tiendas.edit', $t) }}" class="btn-ir-tienda">
                <i class="bi bi-gear"></i> Configurar
            </a>
        </div>
    </div>

</div>
@endforeach

{{-- Superadmins ─────────────────────────────────────────────────── --}}
<div class="chart-card" style="margin-top:1rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
        <div class="chart-title" style="margin-bottom:0;">⚙️ Super Administradores del sistema</div>
        <a href="{{ route('users.create') }}"
           style="font-size:.82rem;background:var(--c-border);border:none;color:var(--c-text);border-radius:8px;padding:.35rem .75rem;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;">
            <i class="bi bi-plus-lg"></i> Agregar
        </a>
    </div>
    <div class="sadmin-list">
        @foreach($superadmins as $sa)
        <div class="sadmin-chip">
            <i class="bi bi-shield-fill-check" style="color:var(--c-accent);"></i>
            {{ $sa->name }}
            @if($sa->id === auth()->id()) <span class="me">vos</span> @endif
            @if($sa->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $sa) }}"
                  onsubmit="return confirm('¿Eliminar superadmin {{ addslashes($sa->name) }}?')"
                  style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:var(--c-muted);cursor:pointer;font-size:.85rem;padding:0;line-height:1;"
                        title="Eliminar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Datos del backend ──────────────────────────────────────────────
const rawData  = @json($ultimos7);
const tiendas  = @json($tiendas->pluck('nombre', 'id'));

// Construir labels (últimos 7 días)
const dias = [];
for (let i = 6; i >= 0; i--) {
    const d = new Date(); d.setDate(d.getDate() - i);
    dias.push(d.toISOString().slice(0, 10));
}
const diaLabels = dias.map(d => {
    const [,m,day] = d.split('-');
    return `${parseInt(day)}/${parseInt(m)}`;
});

// Colores por tienda
const PALETA = ['#f5a623','#2ecc71','#3498db','#e84040','#9b59b6','#1abc9c'];

// Dataset por tienda
const datasets = Object.entries(tiendas).map(([tid, nombre], idx) => {
    const data = dias.map(dia => {
        const fila = rawData.find(r => r.dia === dia && r.tienda_id == tid);
        return fila ? parseFloat(fila.total) : 0;
    });
    return {
        label: nombre,
        data,
        backgroundColor: PALETA[idx % PALETA.length] + '33',
        borderColor:     PALETA[idx % PALETA.length],
        borderWidth: 2,
        tension: 0.3,
        fill: true,
        pointRadius: 3,
    };
});

const ctx = document.getElementById('chartVentas').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: { labels: diaLabels, datasets },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { labels: { color: '#eaeaea', font: { size: 12 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: $${ctx.parsed.y.toLocaleString('es-AR')}`
                }
            }
        },
        scales: {
            x: { ticks: { color: '#7a8085' }, grid: { color: '#2e3235' } },
            y: {
                ticks: {
                    color: '#7a8085',
                    callback: v => '$' + v.toLocaleString('es-AR')
                },
                grid: { color: '#2e3235' }
            }
        }
    }
});
</script>
@endsection
