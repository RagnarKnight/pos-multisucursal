@extends('layouts.app')
@section('title', 'Productos')

@section('extra-css')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .page-title { font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--c-text); }
    .page-title span { color: var(--c-accent); }

    .search-box { position: relative; margin-bottom: 1rem; }
    .search-box input {
        width: 100%;
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 12px;
        color: var(--c-text);
        font-size: 1rem;
        padding: 0.7rem 1rem 0.7rem 2.8rem;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-box input:focus { border-color: var(--c-accent); }
    .search-box input::placeholder { color: var(--c-muted); }
    .search-box .si { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--c-muted); pointer-events:none; }

    .products-table { width: 100%; border-collapse: separate; border-spacing: 0 0.4rem; }
    .products-table thead th {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; color: var(--c-muted);
        padding: 0.4rem 0.75rem; border: none; background: none;
    }
    .products-table tbody tr { background: var(--c-surface); transition: border-color 0.15s; }
    .products-table tbody tr:hover td { border-color: var(--c-accent); }
    .products-table tbody td {
        padding: 0.75rem;
        border-top: 1.5px solid var(--c-border);
        border-bottom: 1.5px solid var(--c-border);
        vertical-align: middle;
    }
    .products-table tbody td:first-child { border-left: 1.5px solid var(--c-border); border-radius: 12px 0 0 12px; }
    .products-table tbody td:last-child  { border-right: 1.5px solid var(--c-border); border-radius: 0 12px 12px 0; }

    .prod-cell { display: flex; align-items: center; gap: 0.7rem; }
    .prod-thumb { width:40px; height:40px; border-radius:8px; object-fit:cover; flex-shrink:0; }
    .prod-thumb-placeholder { width:40px; height:40px; border-radius:8px; background:var(--c-border); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
    .prod-nombre { font-weight:600; font-size:0.9rem; color:var(--c-text); }
    .prod-desc   { font-size:0.75rem; color:var(--c-muted); }

    .precio-venta  { font-family:var(--font-display); font-size:1rem; font-weight:700; color:var(--c-accent); }
    .precio-costo  { font-size:0.78rem; color:var(--c-muted); }
    .margen-badge  { font-size:0.7rem; font-weight:700; background:rgba(46,204,113,0.15); color:#2ecc71; border-radius:5px; padding:0.1rem 0.4rem; }

    /* ── Stock badge ─────────────────────────────────────── */
    .stock-badge { font-family:var(--font-display); font-size:0.95rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:7px; cursor:pointer; transition: opacity 0.15s; }
    .stock-badge:hover { opacity: 0.75; }
    .stock-ok    { background:rgba(46,204,113,0.15); color:#2ecc71; }
    .stock-low   { background:rgba(245,166,35,0.15);  color:#f5a623; }
    .stock-empty { background:rgba(232,64,64,0.15);   color:#e84040; }

    /* ── Edición inline STOCK ────────────────────────────── */
    .inline-stock-form { display:flex; align-items:center; gap:0.3rem; }
    .inline-stock-input {
        background:var(--c-bg); border:1.5px solid #2ecc71;
        border-radius:7px; color:var(--c-text);
        font-family:var(--font-display); font-size:0.95rem; font-weight:700;
        padding:0.25rem 0.4rem; width:70px; outline:none;
    }
    .stock-adj-btn {
        background:var(--c-border); border:none; border-radius:6px;
        color:var(--c-text); width:28px; height:28px;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:1rem; font-weight:700; transition:background 0.15s;
    }
    .stock-adj-btn:hover { background:#2ecc71; color:#111; }

    /* ── Edición inline PRECIO ───────────────────────────── */
    .inline-price-form  { display:flex; align-items:center; gap:0.3rem; }
    .inline-price-input { background:var(--c-bg); border:1.5px solid var(--c-accent); border-radius:7px; color:var(--c-text); font-family:var(--font-display); font-size:0.95rem; font-weight:700; padding:0.25rem 0.4rem; width:90px; outline:none; }
    .inline-save-btn    { background:var(--c-accent); border:none; border-radius:6px; color:#111; font-weight:700; padding:0.25rem 0.5rem; cursor:pointer; font-size:0.8rem; }
    .inline-cancel-btn  { background:none; border:1px solid var(--c-border); border-radius:6px; color:var(--c-muted); padding:0.25rem 0.4rem; cursor:pointer; font-size:0.8rem; }

    .edit-hint { background:none; border:none; color:var(--c-muted); font-size:0.72rem; padding:0; cursor:pointer; margin-top:2px; display:block; }

    .estado-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
    .estado-dot.activo   { background:#2ecc71; }
    .estado-dot.inactivo { background:var(--c-muted); }

    .action-btn { background:none; border:1.5px solid var(--c-border); color:var(--c-muted); border-radius:8px; padding:0.35rem 0.55rem; cursor:pointer; font-size:0.9rem; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; }
    .action-btn:hover        { color:var(--c-accent); border-color:var(--c-accent); }
    .action-btn.danger:hover { color:#e84040; border-color:#e84040; }

    .no-results-row td { text-align:center; color:var(--c-muted); padding:2.5rem; font-size:0.95rem; }

    .pagination .page-link { background:var(--c-surface)!important; border-color:var(--c-border)!important; color:var(--c-text)!important; border-radius:8px!important; margin:0 2px; }
    .pagination .page-link:hover { border-color:var(--c-accent)!important; color:var(--c-accent)!important; }
    .pagination .active .page-link { background:var(--c-accent)!important; color:#111!important; border-color:var(--c-accent)!important; }

    /* ── Alerta stock bajo ───────────────────────────────── */
    .stock-alert-banner {
        background: rgba(232,64,64,0.08);
        border: 1.5px solid rgba(232,64,64,0.3);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
        color: #e84040;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
</style>
@endsection

@section('content')

<div class="page-header"  style="margin-bottom:10px;">
    <div class="page-title">📦 <span>Productos</span></div>
    <a href="{{ route('products.create') }}" class="btn-accent"
       style="font-size:0.95rem; padding:0.6rem 1.1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; border-radius:10px;">
        <i class="bi bi-plus-lg"></i> Nuevo producto
    </a>
</div>

{{-- Alerta de stock bajo --}}
@php $sinStock = $products->filter(fn($p) => $p->stock <= 3); @endphp
@if($sinStock->isNotEmpty())
<div class="stock-alert-banner">
    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem; flex-shrink:0;"></i>
    <span>
        <strong>Stock bajo:</strong>
        {{ $sinStock->map(fn($p) => $p->nombre . ' (' . $p->stock . ')')->join(', ') }}
    </span>
</div>
@endif

{{-- Búsqueda --}}
<div class="search-box">
    <i class="bi bi-search si"></i>
    <input type="text" id="searchInput" placeholder="Buscar por nombre…" autocomplete="off">
</div>

<div style="overflow-x:auto;">
<table class="products-table" id="productsTable">
    <thead>
        <tr>
            <th>PRODUCTO</th>
            <th>PRECIO VENTA</th>
            <th>COSTO</th>
            <th>STOCK <span style="font-size:0.6rem; color:var(--c-muted); font-weight:400;">(tocá para editar)</span></th>
            <th>ESTADO</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @forelse($products as $product)
    <tr data-nombre="{{ strtolower($product->nombre) }}">

        {{-- Nombre + imagen --}}
        <td>
            <div class="prod-cell">
                @if($product->image_path)
                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->nombre }}" class="prod-thumb">
                @else
                    <div class="prod-thumb-placeholder">🛍️</div>
                @endif
                <div>
                    <div class="prod-nombre">{{ $product->nombre }}</div>
                    @if($product->descripcion)
                        <div class="prod-desc">{{ Str::limit($product->descripcion, 35) }}</div>
                    @endif
                </div>
            </div>
        </td>

        {{-- Precio venta — edición rápida inline --}}
        <td>
            <div id="precio-display-{{ $product->id }}">
                <div class="precio-venta">${{ number_format($product->precio_venta, 0, ',', '.') }}</div>
                <button class="edit-hint" onclick="editarPrecio({{ $product->id }}, {{ $product->precio_venta }})">
                    <i class="bi bi-pencil-fill"></i> editar
                </button>
            </div>
            <div id="precio-form-{{ $product->id }}" style="display:none;">
                <form class="inline-price-form" onsubmit="guardarCampo(event, {{ $product->id }}, 'precio')">
                    <input type="number" class="inline-price-input" id="precio-input-{{ $product->id }}"
                           step="0.01" min="0" required>
                    <button type="submit" class="inline-save-btn">✓</button>
                    <button type="button" class="inline-cancel-btn" onclick="cancelar('precio', {{ $product->id }})">✕</button>
                </form>
            </div>
        </td>

        {{-- Costo + margen --}}
        <td>
            <div class="precio-costo">${{ number_format($product->precio_costo, 0, ',', '.') }}</div>
            @if($product->precio_costo > 0)
                <span class="margen-badge">+{{ $product->margenGanancia() }}%</span>
            @endif
        </td>

        {{-- Stock — edición rápida inline --}}
        <td>
            <div id="stock-display-{{ $product->id }}">
                <span class="stock-badge {{ $product->stock == 0 ? 'stock-empty' : ($product->stock <= 3 ? 'stock-low' : 'stock-ok') }}"
                      id="stock-badge-{{ $product->id }}"
                      onclick="editarStock({{ $product->id }}, {{ $product->stock }})"
                      title="Tocá para editar">
                    {{ $product->stock }}
                </span>
            </div>
            <div id="stock-form-{{ $product->id }}" style="display:none;">
                <form class="inline-stock-form" onsubmit="guardarCampo(event, {{ $product->id }}, 'stock')">
                    <button type="button" class="stock-adj-btn"
                            onclick="ajustarStock({{ $product->id }}, -1)">−</button>
                    <input type="number" class="inline-stock-input" id="stock-input-{{ $product->id }}"
                           min="0" step="1" required>
                    <button type="button" class="stock-adj-btn"
                            onclick="ajustarStock({{ $product->id }}, 1)">+</button>
                    <button type="submit" class="inline-save-btn" style="background:#2ecc71;">✓</button>
                    <button type="button" class="inline-cancel-btn" onclick="cancelar('stock', {{ $product->id }})">✕</button>
                </form>
            </div>
        </td>

        {{-- Estado --}}
        <td>
            <span class="estado-dot {{ $product->activo ? 'activo' : 'inactivo' }}"></span>
            <span style="font-size:0.82rem; color:var(--c-muted);">
                {{ $product->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </td>

        {{-- Acciones --}}
        <td>
            <div style="display:flex; gap:0.3rem; justify-content:flex-end;">
                <a href="{{ route('products.edit', $product) }}" class="action-btn" title="Editar todo">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('¿Desactivar {{ addslashes($product->nombre) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn danger" title="Desactivar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr class="no-results-row">
        <td colspan="6">
            <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
            No hay productos. <a href="{{ route('products.create') }}" style="color:var(--c-accent);">Crear el primero</a>
        </td>
    </tr>
    @endforelse

    <tr id="noResultsRow" style="display:none;" class="no-results-row">
        <td colspan="6"><i class="bi bi-search"></i> Sin resultados.</td>
    </tr>
    </tbody>
</table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $products->links() }}
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Búsqueda ──────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('#productsTable tbody tr[data-nombre]').forEach(row => {
        const match = row.dataset.nombre.includes(term);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noResultsRow').style.display = (visible === 0 && term) ? '' : 'none';
});

// ── Edición precio ────────────────────────────────────────────
function editarPrecio(id, actual) {
    document.getElementById(`precio-display-${id}`).style.display = 'none';
    document.getElementById(`precio-form-${id}`).style.display = 'block';
    const input = document.getElementById(`precio-input-${id}`);
    input.value = actual; input.focus(); input.select();
}

// ── Edición stock ─────────────────────────────────────────────
function editarStock(id, actual) {
    document.getElementById(`stock-display-${id}`).style.display = 'none';
    document.getElementById(`stock-form-${id}`).style.display = 'flex';
    const input = document.getElementById(`stock-input-${id}`);
    input.value = actual; input.focus(); input.select();
}

function ajustarStock(id, delta) {
    const input = document.getElementById(`stock-input-${id}`);
    const nuevo = Math.max(0, (parseInt(input.value) || 0) + delta);
    input.value = nuevo;
}

// ── Cancelar edición ──────────────────────────────────────────
function cancelar(tipo, id) {
    document.getElementById(`${tipo}-display-${id}`).style.display = 'block';
    document.getElementById(`${tipo}-form-${id}`).style.display = 'none';
}

// ── Guardar campo (precio o stock) vía fetch ─────────────────
async function guardarCampo(e, id, tipo) {
    e.preventDefault();
    const input = document.getElementById(`${tipo}-input-${id}`);
    const valor = parseFloat(input.value);
    if (isNaN(valor) || valor < 0) return;

    const body = tipo === 'precio'
        ? { precio_venta: valor }
        : { stock: valor };

    try {
        const res = await fetch(`/products/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });

        if (res.ok) {
            const data = await res.json();

            if (tipo === 'precio') {
                document.querySelector(`#precio-display-${id} .precio-venta`).textContent =
                    '$' + fmt(valor);
            } else {
                // Actualizar badge de stock con color correcto
                const badge = document.getElementById(`stock-badge-${id}`);
                badge.textContent = valor;
                badge.className = 'stock-badge ' + (
                    valor == 0 ? 'stock-empty' : valor <= 3 ? 'stock-low' : 'stock-ok'
                );
            }

            cancelar(tipo, id);
            toast(tipo === 'precio' ? '✅ Precio actualizado' : '✅ Stock actualizado');
        } else {
            toast('❌ Error al guardar', 'danger');
        }
    } catch {
        toast('❌ Error de conexión', 'danger');
    }
}

// ── Helpers ───────────────────────────────────────────────────
function fmt(n) {
    return new Intl.NumberFormat('es-AR', { minimumFractionDigits: 0 }).format(n);
}

function toast(msg, tipo = 'success') {
    const colors = {
        success: { bg:'#1a4731', color:'#2ecc71', border:'#2ecc71' },
        danger:  { bg:'#3d1f1f', color:'#e84040', border:'#e84040' },
    };
    const c = colors[tipo];
    const container = document.querySelector('.flash-container');
    const div = document.createElement('div');
    div.className = 'alert flash-alert alert-dismissible fade show';
    div.style.cssText = `background:${c.bg};color:${c.color};border-left:4px solid ${c.border};`;
    div.innerHTML = `${msg} <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>`;
    container.appendChild(div);
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(div).close(), 2500);
}
</script>
@endsection
