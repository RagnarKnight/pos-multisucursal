@extends('layouts.app')
@section('title', 'Orden #' . $order->id)

@section('extra-css')
<style>
    .order-detail-wrapper { max-width: 560px; margin: 0 auto; }

    .order-hero {
        background: var(--c-surface); border: 1.5px solid var(--c-border);
        border-radius: 16px; padding: 1.25rem; margin-bottom: 1rem;
        position: relative; overflow: hidden;
    }
    .order-hero::before {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        background: var(--c-accent);
    }
    .order-id-badge { font-size:0.75rem; color:var(--c-muted); font-weight:600; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:0.25rem; }
    .order-total-big { font-family:var(--font-display); font-size:2.8rem; font-weight:800; color:var(--c-accent); line-height:1; margin-bottom:0.5rem; }
    .order-meta { display:flex; flex-wrap:wrap; gap:0.6rem; align-items:center; }

    .meta-chip { display:inline-flex; align-items:center; gap:0.3rem; font-size:0.8rem; padding:0.25rem 0.6rem; border-radius:20px; font-weight:600; border:1px solid transparent; }
    .chip-efectivo      { background:#1a4731; color:#2ecc71; border-color:rgba(46,204,113,0.3); }
    .chip-transferencia { background:#1a2e47; color:#3498db; border-color:rgba(52,152,219,0.3); }
    .chip-fiado         { background:#3d1f1f; color:#e84040; border-color:rgba(232,64,64,0.3); }
    .chip-neutral       { background:var(--c-border); color:var(--c-muted); }

    .items-card { background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:14px; overflow:hidden; margin-bottom:1rem; }
    .items-card-header { padding:0.75rem 1.1rem; border-bottom:1px solid var(--c-border); font-size:0.75rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.08em; }

    .item-row { display:flex; align-items:center; padding:0.8rem 1.1rem; border-bottom:1px solid var(--c-border); gap:0.75rem; }
    .item-row:last-child { border-bottom:none; }
    .item-qty { background:var(--c-border); color:var(--c-text); font-family:var(--font-display); font-size:0.85rem; font-weight:700; border-radius:6px; padding:0.2rem 0.5rem; min-width:32px; text-align:center; flex-shrink:0; }
    .item-name { flex:1; font-size:0.9rem; font-weight:500; color:var(--c-text); }
    .item-unit-price { font-size:0.75rem; color:var(--c-muted); }
    .item-subtotal { font-family:var(--font-display); font-size:1rem; font-weight:700; color:var(--c-text); text-align:right; min-width:80px; }

    .totals-card { background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:14px; overflow:hidden; margin-bottom:1rem; }
    .total-row { display:flex; justify-content:space-between; padding:0.7rem 1.1rem; border-bottom:1px solid var(--c-border); font-size:0.88rem; }
    .total-row:last-child { border-bottom:none; }
    .total-row.final { background:rgba(245,166,35,0.06); font-size:1rem; }
    .total-row .label { color:var(--c-muted); }
    .total-row .value { font-family:var(--font-display); font-weight:700; color:var(--c-text); }
    .total-row.final .value { color:var(--c-accent); font-size:1.3rem; }

    .comprobante-card { background:var(--c-surface); border:1.5px solid #3498db; border-radius:14px; padding:1rem; margin-bottom:1rem; }
    .comprobante-card img { width:100%; max-height:300px; object-fit:contain; border-radius:8px; background:var(--c-bg); }

    /* ── Cliente fiado + edición ─────────────────────────── */
    .cliente-card {
        background: rgba(232,64,64,0.06); border: 1.5px solid rgba(232,64,64,0.3);
        border-radius: 14px; padding: 1rem 1.1rem; margin-bottom: 1rem;
    }
    .cliente-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem; }
    .cliente-card .nombre { font-weight:600; color:var(--c-text); }
    .cliente-card .saldo  { font-family:var(--font-display); font-size:1.1rem; font-weight:800; color:#e84040; }

    .btn-editar-cliente {
        background: none; border: 1px solid rgba(232,64,64,0.4); color: #e84040;
        border-radius: 8px; font-size: 0.78rem; padding: 0.25rem 0.6rem;
        cursor: pointer; transition: background 0.15s;
    }
    .btn-editar-cliente:hover { background: rgba(232,64,64,0.1); }

    .editar-cliente-form { display:none; margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(232,64,64,0.2); }
    .editar-cliente-form.show { display:block; }
    .editar-cliente-form select {
        width:100%; background:var(--c-bg); border:1.5px solid rgba(232,64,64,0.5);
        border-radius:10px; color:var(--c-text); padding:0.6rem 0.75rem;
        font-size:0.9rem; outline:none; margin-bottom:0.6rem;
    }
    .editar-cliente-form select:focus { border-color:#e84040; }
    .form-actions { display:flex; gap:0.5rem; }

    .detail-actions { display:flex; gap:0.6rem; flex-wrap:wrap; }

    /* ── CSS de impresión ────────────────────────────────── */
    @media print {
        /* Ocultar todo el chrome de la app */
        .pos-navbar,
        .sidebar-offcanvas,
        .breadcrumb-back,
        .detail-actions,
        .btn-editar-cliente,
        .editar-cliente-form,
        .flash-container,
        .offcanvas-backdrop       { display: none !important; }

        body, html {
            background: #fff !important;
            color: #000 !important;
            font-size: 12pt;
        }

        .order-detail-wrapper {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Variables override para impresión */
        :root {
            --c-bg:      #fff;
            --c-surface: #fff;
            --c-border:  #ddd;
            --c-text:    #000;
            --c-muted:   #555;
            --c-accent:  #c4841a;
        }

        .order-hero { border: 2px solid #ccc; border-radius: 8px; break-inside: avoid; }
        .order-hero::before { background: #c4841a; }

        .order-total-big { color: #c4841a; font-size: 2rem; }

        .meta-chip {
            border: 1px solid #ccc !important;
            background: #f5f5f5 !important;
            color: #333 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .items-card, .totals-card, .cliente-card, .comprobante-card {
            border: 1px solid #ccc;
            border-radius: 6px;
            break-inside: avoid;
        }

        .item-row, .total-row { border-bottom-color: #eee; }

        .item-qty {
            background: #eee !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .total-row.final {
            background: #fff8ee !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .total-row.final .value { color: #c4841a; }

        .cliente-card {
            background: #fff8f8 !important;
            border-color: #e84040 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Pie del comprobante */
        .print-footer {
            display: block !important;
            text-align: center;
            font-size: 9pt;
            color: #888;
            margin-top: 1.5rem;
            border-top: 1px dashed #ccc;
            padding-top: 0.5rem;
        }
    }

    /* Ocultar el pie en pantalla */
    .print-footer { display: none; }
</style>
@endsection

@section('content')
<div class="order-detail-wrapper">

    <div class="breadcrumb-back" style="margin-bottom:1rem;">
        <a href="{{ route('orders.index') }}"
           style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> Volver al historial
        </a>
    </div>

    {{-- Hero --}}
    <div class="order-hero">
        <div class="order-id-badge">Orden #{{ $order->id }}</div>
        <div class="order-total-big">${{ number_format($order->total, 0, ',', '.') }}</div>
        <div class="order-meta">
            <span class="meta-chip chip-{{ $order->metodo_pago }}">
                @if($order->metodo_pago === 'efectivo')          <i class="bi bi-cash-coin"></i> Efectivo
                @elseif($order->metodo_pago === 'transferencia') <i class="bi bi-phone"></i> Transferencia
                @else                                            <i class="bi bi-book"></i> Fiado
                @endif
            </span>
            <span class="meta-chip chip-neutral">
                <i class="bi bi-calendar3"></i> {{ $order->created_at->format('d/m/Y H:i') }}
            </span>
            <span class="meta-chip chip-neutral">
                <i class="bi bi-person"></i> {{ $order->user->name }}
            </span>
        </div>
        @if($order->notas)
        <div style="margin-top:0.75rem; font-size:0.85rem; color:var(--c-muted); font-style:italic;">
            <i class="bi bi-chat-left-text"></i> {{ $order->notas }}
        </div>
        @endif
    </div>

    {{-- Cliente fiado — con edición si es admin --}}
    @if($order->metodo_pago === 'fiado')
    <div class="cliente-card">
        <div class="cliente-card-header">
            <div>
                <div style="font-size:0.72rem; color:#e84040; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.2rem;">
                    <i class="bi bi-book"></i> Venta fiada a:
                </div>
                <div class="nombre">{{ $order->customer->nombre ?? '—' }}</div>
                @if($order->customer?->telefono)
                    <div style="font-size:0.78rem; color:var(--c-muted);">
                        <i class="bi bi-telephone"></i> {{ $order->customer->telefono }}
                    </div>
                @endif
            </div>
            <div class="text-end">
                @if($order->customer)
                <div style="font-size:0.72rem; color:var(--c-muted);">Saldo actual</div>
                <div class="saldo">${{ number_format($order->customer->saldo_deudor, 0, ',', '.') }}</div>
                <a href="{{ route('customers.show', $order->customer) }}"
                   style="font-size:0.75rem; color:#e84040; text-decoration:none;">Ver libreta →</a>
                @endif
            </div>
        </div>

        {{-- Formulario para cambiar cliente (admin) --}}
        @can('admin')
        <button class="btn-editar-cliente" onclick="toggleEditarCliente()">
            <i class="bi bi-pencil"></i> Cambiar cliente
        </button>

        <div class="editar-cliente-form" id="editarClienteForm">
            <form method="POST" action="{{ route('orders.update', $order) }}">
                @csrf @method('PATCH')
                <div style="font-size:0.8rem; color:var(--c-muted); margin-bottom:0.5rem;">
                    Reasignar la deuda de ${{ number_format($order->total, 0, ',', '.') }} a:
                </div>
                <select name="customer_id" required>
                    <option value="">— Seleccioná el cliente —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $order->customer_id == $c->id ? 'selected' : '' }}>
                        {{ $c->nombre }}
                        @if($c->saldo_deudor > 0) (Debe ${{ number_format($c->saldo_deudor, 0, ',', '.') }})@endif
                    </option>
                    @endforeach
                </select>
                <div class="form-actions">
                    <button type="submit" class="btn-accent" style="border-radius:8px; padding:0.5rem 1rem; font-size:0.85rem;">
                        <i class="bi bi-check-lg"></i> Confirmar
                    </button>
                    <button type="button" onclick="toggleEditarCliente()"
                            style="background:none; border:1px solid var(--c-border); color:var(--c-muted); border-radius:8px; padding:0.5rem 0.75rem; cursor:pointer; font-size:0.85rem;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
        @endcan
    </div>
    @endif

    {{-- Comprobante transferencia --}}
    @if($order->metodo_pago === 'transferencia')
    <div class="comprobante-card" id="comprobanteCard">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
            <div style="font-size:0.75rem;color:#3498db;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">
                <i class="bi bi-image"></i> Comprobante de transferencia
            </div>
                {{-- Cualquier usuario logueado puede subir/reemplazar su comprobante --}}
            <button type="button" onclick="toggleUploadForm()"
                    style="background:none;border:1px solid rgba(52,152,219,0.4);color:#3498db;border-radius:7px;font-size:0.75rem;padding:0.2rem 0.55rem;cursor:pointer;display:flex;align-items:center;gap:0.3rem;">
                <i class="bi bi-{{ $order->comprobante_path ? 'arrow-repeat' : 'upload' }}"></i>
                {{ $order->comprobante_path ? 'Reemplazar foto' : 'Subir comprobante' }}
            </button>
        </div>

        {{-- Imagen actual --}}
        @if($order->comprobante_path)
        <img src="{{ Storage::url($order->comprobante_path) }}" alt="Comprobante"
             id="comprobanteImg"
             style="width:100%;max-height:300px;object-fit:contain;border-radius:8px;background:var(--c-bg);cursor:pointer;"
             onclick="this.style.maxHeight = this.style.maxHeight === 'none' ? '300px' : 'none'">
        <div style="font-size:0.7rem;color:var(--c-muted);text-align:center;margin-top:0.3rem;">
            Tocá la imagen para ampliar
        </div>
        @else
        <div style="text-align:center;padding:1.5rem 0;color:var(--c-muted);">
            <i class="bi bi-image" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
            Sin comprobante adjunto
        </div>
        @endif

        {{-- Formulario de upload (oculto por defecto) --}}
        <div id="uploadForm" style="display:none;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid rgba(52,152,219,0.2);">
            <form method="POST" action="{{ route('orders.comprobante', $order) }}"
                  enctype="multipart/form-data" id="comprobanteForm">
                @csrf @method('PATCH')

                {{-- Drop zone --}}
                <div id="uploadDropZone"
                     style="border:2px dashed rgba(52,152,219,0.4);border-radius:10px;padding:1rem;text-align:center;cursor:pointer;transition:all 0.15s;background:rgba(52,152,219,0.03);"
                     onclick="document.getElementById('comprobanteFileInput').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#3498db';this.style.background='rgba(52,152,219,0.08)'"
                     ondragleave="this.style.borderColor='rgba(52,152,219,0.4)';this.style.background='rgba(52,152,219,0.03)'">

                    <img id="uploadPreview" src="" alt=""
                         style="display:none;max-height:120px;max-width:100%;object-fit:contain;border-radius:6px;margin-bottom:0.5rem;">

                    <div id="uploadPlaceholder">
                        <i class="bi bi-camera" style="font-size:1.6rem;color:#3498db;display:block;margin-bottom:0.3rem;"></i>
                        <div style="font-size:0.82rem;color:var(--c-muted);">Tocá para sacar foto o elegir imagen</div>
                        <div style="font-size:0.72rem;color:var(--c-muted);margin-top:0.2rem;">JPG, PNG — máx 5MB</div>
                    </div>

                    <input type="file" id="comprobanteFileInput" name="comprobante"
                           accept="image/*"
                           style="display:none;" onchange="previewComprobante(this)">
                </div>

                <div style="display:flex;gap:0.5rem;margin-top:0.6rem;">
                    <button type="submit" id="btnSubirComprobante"
                            class="btn-accent" disabled
                            style="border-radius:8px;padding:0.5rem 1rem;font-size:0.88rem;opacity:0.5;flex:1;">
                        <i class="bi bi-check-lg"></i> Guardar comprobante
                    </button>
                    <button type="button" onclick="toggleUploadForm()"
                            style="background:none;border:1px solid var(--c-border);color:var(--c-muted);border-radius:8px;padding:0.5rem 0.75rem;cursor:pointer;font-size:0.88rem;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

    </div>
    @endif

    {{-- Items --}}
    <div class="items-card">
        <div class="items-card-header">
            {{ $order->items->sum('cantidad') }} unidades · {{ $order->items->count() }} producto{{ $order->items->count() > 1 ? 's' : '' }}
        </div>
        @foreach($order->items as $item)
        <div class="item-row">
            <span class="item-qty">{{ $item->cantidad }}x</span>
            <div class="item-name">
                {{ $item->product->nombre }}
                <div class="item-unit-price">${{ number_format($item->precio_unitario, 0, ',', '.') }} c/u</div>
            </div>
            <div class="item-subtotal">${{ number_format($item->subtotal, 0, ',', '.') }}</div>
        </div>
        @endforeach
    </div>

    {{-- Totales --}}
    <div class="totals-card">
        <div class="total-row">
            <span class="label">Unidades</span>
            <span class="value">{{ $order->items->sum('cantidad') }}</span>
        </div>
        <div class="total-row final">
            <span class="label">TOTAL</span>
            <span class="value">${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Pie solo visible al imprimir --}}
    <div class="print-footer">
        Orden #{{ $order->id }} · {{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->user->name }}<br>
        POS Barrio Santa Fe
    </div>

    {{-- Acciones --}}
    <div class="detail-actions">
        <a href="{{ route('orders.index') }}" class="btn-outline-ghost"
           style="display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button onclick="window.print()" class="btn-outline-ghost"
                style="display:inline-flex; align-items:center; gap:0.4rem; cursor:pointer;">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <a href="{{ route('pos.index') }}" class="btn-accent"
           style="display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none; border-radius:10px; margin-left:auto;">
            <i class="bi bi-grid-3x3-gap"></i> Nueva venta
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
function toggleEditarCliente() {
    document.getElementById('editarClienteForm').classList.toggle('show');
}

// ── Upload de comprobante desde historial ─────────────────────
// En móvil abre la cámara, en desktop el explorador de archivos
const esMobil = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
if (esMobil) {
    const fi = document.getElementById('comprobanteFileInput');
    if (fi) fi.setAttribute('capture', 'environment');
}
function toggleUploadForm() {
    const form = document.getElementById('uploadForm');
    const showing = form.style.display === 'none' || !form.style.display;
    form.style.display = showing ? 'block' : 'none';
}

function previewComprobante(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('uploadPreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        const btn = document.getElementById('btnSubirComprobante');
        preview.src = e.target.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
        btn.disabled = false;
        btn.style.opacity = '1';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
