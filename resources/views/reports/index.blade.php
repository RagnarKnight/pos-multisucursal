@extends('layouts.app')
@section('title', 'Reportes')

@section('extra-css')
<style>
    .page-title { font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-bottom:1.25rem; }
    .page-title span { color:var(--c-accent); }

    /* ── Selector de período ─────────────────────────────── */
    .period-selector {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .period-select {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        color: var(--c-text);
        padding: 0.55rem 0.85rem;
        font-size: 0.9rem;
        outline: none;
        cursor: pointer;
    }
    .period-select:focus { border-color: var(--c-accent); }
    .period-label { font-size: 0.82rem; color: var(--c-muted); align-self: center; }

    /* ── KPI cards ───────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.6rem;
        margin-bottom: 1.25rem;
    }
    @media (min-width: 576px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 992px) { .kpi-grid { grid-template-columns: repeat(6, 1fr); } }

    .kpi-card {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        text-align: center;
    }
    .kpi-card.highlight { border-color: var(--c-accent); background: linear-gradient(135deg, var(--c-surface), rgba(245,166,35,0.06)); }
    .kpi-label  { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--c-muted); margin-bottom: 0.3rem; }
    .kpi-value  { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; }
    .kpi-value.verde  { color: #2ecc71; }
    .kpi-value.azul   { color: #3498db; }
    .kpi-value.rojo   { color: #e84040; }
    .kpi-value.accent { color: var(--c-accent); }
    .kpi-value.blanco { color: var(--c-text); }

    /* ── Gráfico de barras ───────────────────────────────── */
    .chart-card {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .chart-title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--c-text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chart-empty { text-align: center; color: var(--c-muted); padding: 2rem; font-size: 0.9rem; }

    /* ── Grid de dos columnas ────────────────────────────── */
    .two-col { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem; }
    @media (min-width: 768px) { .two-col { grid-template-columns: 1fr 1fr; } }

    /* ── Top productos ───────────────────────────────────── */
    .top-item {
        display: flex;
        align-items: center;
        padding: 0.65rem 1rem;
        border-bottom: 1px solid var(--c-border);
        gap: 0.75rem;
    }
    .top-item:last-child { border-bottom: none; }
    .top-rank {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 800;
        color: var(--c-muted);
        min-width: 24px;
        text-align: center;
    }
    .top-rank.gold   { color: #f5a623; }
    .top-rank.silver { color: #aaa; }
    .top-rank.bronze { color: #cd7f32; }
    .top-name  { flex: 1; font-size: 0.88rem; font-weight: 500; color: var(--c-text); }
    .top-units { font-family: var(--font-display); font-size: 0.85rem; color: var(--c-muted); }
    .top-total { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--c-accent); text-align: right; min-width: 80px; }

    /* Barra de progreso relativa */
    .top-bar { height: 3px; background: var(--c-border); border-radius: 2px; margin-top: 4px; }
    .top-bar-fill { height: 100%; background: var(--c-accent); border-radius: 2px; transition: width 0.5s ease; }

    /* ── Deudores ────────────────────────────────────────── */
    .deudor-row {
        display: flex;
        align-items: center;
        padding: 0.65rem 1rem;
        border-bottom: 1px solid var(--c-border);
        gap: 0.75rem;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }
    .deudor-row:last-child { border-bottom: none; }
    .deudor-row:hover { background: rgba(232,64,64,0.04); }
    .deudor-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: var(--c-border);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 0.85rem; font-weight: 800; color: var(--c-accent);
        flex-shrink: 0;
    }
    .deudor-nombre { flex: 1; font-size: 0.88rem; font-weight: 500; color: var(--c-text); }
    .deudor-saldo  { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: #e84040; }

    /* ── Sin stock ───────────────────────────────────────── */
    .sin-stock-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 1rem;
        border-bottom: 1px solid var(--c-border);
        font-size: 0.88rem;
    }
    .sin-stock-row:last-child { border-bottom: none; }

    /* ── Tabla diaria ────────────────────────────────────── */
    .day-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .day-table th { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--c-muted); padding: 0.4rem 0.75rem; text-align: left; border-bottom: 1px solid var(--c-border); }
    .day-table td { padding: 0.6rem 0.75rem; border-bottom: 1px solid var(--c-border); color: var(--c-text); }
    .day-table tr:last-child td { border-bottom: none; }
    .day-table tr:hover td { background: rgba(245,166,35,0.04); }
    .day-total { font-family: var(--font-display); font-weight: 700; color: var(--c-accent); }
</style>
@endsection

@section('content')

<div class="page-title">📊 <span>Reportes</span></div>

{{-- Selector período --}}
<form method="GET" action="{{ route('reports.index') }}" class="period-selector">
    <span class="period-label">Ver mes:</span>
    <select name="mes" class="period-select" onchange="this.form.submit()">
        @foreach($meses as $num => $nombre)
            <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
        @endforeach
    </select>
    <select name="anio" class="period-select" onchange="this.form.submit()">
        @foreach($anios as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
    </select>
    <span class="period-label" style="color:var(--c-accent);">
        {{ $meses[$mes] }} {{ $anio }}
    </span>
</form>

{{-- KPIs --}}
<div class="kpi-grid">
    <div class="kpi-card highlight">
        <div class="kpi-label">💰 Total cobrado</div>
        <div class="kpi-value accent">${{ number_format($totalesMes['efectivo'] + $totalesMes['transferencia'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label"><i class="bi bi-cash-coin"></i> Efectivo</div>
        <div class="kpi-value verde">${{ number_format($totalesMes['efectivo'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label"><i class="bi bi-phone"></i> Transferencia</div>
        <div class="kpi-value azul">${{ number_format($totalesMes['transferencia'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label"><i class="bi bi-book"></i> Fiado</div>
        <div class="kpi-value rojo">${{ number_format($totalesMes['fiado'], 0, ',', '.') }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">🧾 Ventas</div>
        <div class="kpi-value blanco">{{ $totalesMes['ordenes'] }}</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">🎫 Promedio por venta.</div>
        <div class="kpi-value blanco">${{ number_format($totalesMes['ticket_prom'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Gráfico de barras por día --}}
<div class="chart-card">
    <div class="chart-title"><i class="bi bi-bar-chart-fill" style="color:var(--c-accent);"></i> Ventas diarias</div>
    @if($ventasPorDia->isEmpty())
        <div class="chart-empty"><i class="bi bi-bar-chart" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>Sin datos para este período.</div>
    @else
        <canvas id="ventasChart" height="80"></canvas>
    @endif
</div>

<div class="two-col">

    {{-- Top productos --}}
    <div class="chart-card" style="margin-bottom:0;">
        <div class="chart-title"><i class="bi bi-trophy-fill" style="color:#f5a623;"></i> Top productos del mes</div>
        @if($topProductos->isEmpty())
            <div class="chart-empty">Sin ventas registradas.</div>
        @else
            @php $maxUnidades = $topProductos->first()->total_unidades; @endphp
            @foreach($topProductos as $i => $item)
            <div class="top-item">
                <div class="top-rank {{ $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')) }}">
                    #{{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div class="top-name">{{ $item->product->nombre ?? 'Producto eliminado' }}</div>
                    <div class="top-bar">
                        <div class="top-bar-fill" style="width:{{ ($item->total_unidades / $maxUnidades) * 100 }}%"></div>
                    </div>
                </div>
                <div class="top-units">{{ $item->total_unidades }} u.</div>
                <div class="top-total">${{ number_format($item->total_ingresos, 0, ',', '.') }}</div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Columna derecha: deudores + sin stock --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">

        {{-- Top deudores --}}
        <div class="chart-card" style="margin-bottom:0; flex:1;">
            <div class="chart-title"><i class="bi bi-people-fill" style="color:#e84040;"></i> Mayores deudores</div>
            @if($topDeudores->isEmpty())
                <div class="chart-empty" style="padding:1rem;">¡Nadie debe! 🎉</div>
            @else
                @foreach($topDeudores as $d)
                <a href="{{ route('customers.show', $d) }}" class="deudor-row">
                    <div class="deudor-avatar">{{ strtoupper(substr($d->nombre, 0, 2)) }}</div>
                    <div class="deudor-nombre">{{ $d->nombre }}</div>
                    <div class="deudor-saldo">${{ number_format($d->saldo_deudor, 0, ',', '.') }}</div>
                </a>
                @endforeach
            @endif
        </div>

        {{-- Productos sin stock --}}
        <div class="chart-card" style="margin-bottom:0; flex:1;">
            <div class="chart-title"><i class="bi bi-box-seam" style="color:#e84040;"></i> Sin stock</div>
            @if($sinStock->isEmpty())
                <div class="chart-empty" style="padding:1rem;">✅ Todo con stock.</div>
            @else
                @foreach($sinStock as $p)
                <div class="sin-stock-row">
                    <span style="color:var(--c-text);">{{ $p->nombre }}</span>
                    <a href="{{ route('products.edit', $p) }}" style="color:var(--c-accent); font-size:0.8rem; text-decoration:none;">
                        <i class="bi bi-pencil"></i> Reponer
                    </a>
                </div>
                @endforeach
            @endif
        </div>

    </div>
</div>

{{-- Detalle diario --}}
@if($ventasPorDia->isNotEmpty())
<div class="chart-card" style="margin-top:1rem;">
    <div class="chart-title"><i class="bi bi-calendar3" style="color:var(--c-accent);"></i> Detalle por día</div>
    <div style="overflow-x:auto;">
    <table class="day-table">
        <thead>
            <tr>
                <th>FECHA</th>
                <th>EFECTIVO</th>
                <th>TRANSFER</th>
                <th>FIADO</th>
                <th>ÓRDENES</th>
                <th>TOTAL DÍA</th>
            </tr>
        </thead>
        <tbody>
        @foreach($ventasPorDia as $dia)
        <tr>
            <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
            <td style="color:#2ecc71;">${{ number_format($dia->efectivo, 0, ',', '.') }}</td>
            <td style="color:#3498db;">${{ number_format($dia->transferencia, 0, ',', '.') }}</td>
            <td style="color:#e84040;">${{ number_format($dia->fiado, 0, ',', '.') }}</td>
            <td style="color:var(--c-muted);">{{ $dia->cantidad }}</td>
            <td class="day-total">${{ number_format($dia->total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top: 2px solid var(--c-accent);">
                <td style="font-weight:700; color:var(--c-muted);">TOTAL MES</td>
                <td style="font-family:var(--font-display); color:#2ecc71; font-weight:700;">${{ number_format($totalesMes['efectivo'], 0, ',', '.') }}</td>
                <td style="font-family:var(--font-display); color:#3498db; font-weight:700;">${{ number_format($totalesMes['transferencia'], 0, ',', '.') }}</td>
                <td style="font-family:var(--font-display); color:#e84040; font-weight:700;">${{ number_format($totalesMes['fiado'], 0, ',', '.') }}</td>
                <td style="font-family:var(--font-display); color:var(--c-muted); font-weight:700;">{{ $totalesMes['ordenes'] }}</td>
                <td class="day-total" style="font-size:1.1rem;">${{ number_format($totalesMes['total'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
@endif

@endsection

@section('scripts')
{{-- Chart.js desde CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
@if(!$ventasPorDia->isEmpty())
const grafico = @json($graficoDias);

const ctx = document.getElementById('ventasChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: grafico.labels,
        datasets: [
            {
                label: 'Efectivo',
                data: grafico.efectivo,
                backgroundColor: 'rgba(46,204,113,0.7)',
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Transfer',
                data: grafico.transferencia,
                backgroundColor: 'rgba(52,152,219,0.7)',
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Fiado',
                data: grafico.fiado,
                backgroundColor: 'rgba(232,64,64,0.6)',
                borderRadius: 5,
                borderSkipped: false,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#eaeaea', font: { size: 12 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ' $' + new Intl.NumberFormat('es-AR').format(ctx.raw)
                }
            }
        },
        scales: {
            x: {
                stacked: true,
                ticks: { color: '#7a8085', font: { size: 11 } },
                grid:  { color: '#2e3235' },
            },
            y: {
                stacked: true,
                ticks: {
                    color: '#7a8085',
                    callback: v => '$' + new Intl.NumberFormat('es-AR').format(v)
                },
                grid: { color: '#2e3235' },
            }
        }
    }
});
@endif
</script>
@endsection
