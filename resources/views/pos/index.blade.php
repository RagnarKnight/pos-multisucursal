@extends('layouts.app')
@section('title', 'POS — Punto de Venta')

@section('extra-css')
<style>
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 992px) {
        .pos-layout { grid-template-columns: 1fr 360px; align-items: start; }
    }

    /* ── Búsqueda ────────────────────────────────────────── */
    .search-box { position: relative; margin-bottom: 1rem; }
    .search-box input {
        width: 100%; background: var(--c-surface); border: 1.5px solid var(--c-border);
        border-radius: 12px; color: var(--c-text); font-size: 1rem;
        padding: 0.75rem 1rem 0.75rem 2.8rem; outline: none; transition: border-color 0.2s;
    }
    .search-box input:focus { border-color: var(--c-accent); }
    .search-box input::placeholder { color: var(--c-muted); }
    .search-icon { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--c-muted); pointer-events:none; }

    /* ── Cuadrícula de productos ─────────────────────────── */
    .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
    @media (min-width: 480px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 768px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (min-width: 992px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }

    .product-card {
        background: var(--c-surface); border: 1.5px solid var(--c-border); border-radius: 14px;
        padding: 0.75rem 0.6rem; cursor: pointer; user-select: none;
        display: flex; flex-direction: column; align-items: center; text-align: center;
        gap: 0.4rem; transition: border-color 0.15s, transform 0.1s, background 0.15s;
        -webkit-tap-highlight-color: transparent; min-height: 110px; justify-content: center;
        position: relative; overflow: hidden;
    }
    .product-card:active  { transform: scale(0.95); }
    .product-card:hover   { border-color: var(--c-accent); background: rgba(245,166,35,0.06); }
    .product-card.sin-stock { opacity: 0.4; pointer-events: none; }

    .prod-img   { width:52px; height:52px; object-fit:cover; border-radius:8px; }
    .prod-emoji { font-size:2rem; line-height:1; }
    .prod-name  { font-size:0.78rem; font-weight:600; color:var(--c-text); line-height:1.2; max-height:2.4em; overflow:hidden; }
    .prod-price { font-family:var(--font-display); font-size:1.05rem; font-weight:700; color:var(--c-accent); }
    .prod-stock { font-size:0.68rem; color:var(--c-muted); }
    .prod-stock.low { color:#e84040; }

    /* Badge de cantidad en la card ── MEJORADO */
    .badge-in-cart {
        position: absolute; top:6px; right:6px;
        background: var(--c-accent); color: #111;
        font-size: 0.7rem; font-weight: 800;
        border-radius: 50%; width:22px; height:22px;
        display: flex; align-items:center; justify-content:center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        animation: popIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes popIn {
        from { transform: scale(0); }
        to   { transform: scale(1); }
    }

    .no-results { grid-column:1/-1; text-align:center; color:var(--c-muted); padding:2rem; font-size:0.95rem; }

    /* ── Carrito flotante ─────────────────────────────────── */
    .cart-panel {
        background: var(--c-surface); border: 1.5px solid var(--c-border); border-radius: 16px; overflow: hidden;
    }
    @media (max-width: 991px) {
        .cart-panel {
            position: fixed; bottom:0; left:0; right:0;
            border-radius: 20px 20px 0 0; border-bottom: none; z-index: 500;
            max-height: 70vh;
            transform: translateY(calc(100% - 72px));
            transition: transform 0.35s cubic-bezier(0.32,0.72,0,1);
        }
        .cart-panel.open { transform: translateY(0); }
        .main-content { padding-bottom: 90px; }
    }
    @media (min-width: 992px) {
        .cart-panel { position: sticky; top: calc(var(--nav-h) + 1rem); }
    }

    .cart-header {
        display:flex; align-items:center; justify-content:space-between;
        padding: 1rem 1rem 0.75rem; cursor:pointer; border-bottom: 1px solid var(--c-border);
    }
    @media (min-width: 992px) { .cart-header { cursor: default; } }

    .cart-title { font-family:var(--font-display); font-size:1.15rem; font-weight:700; color:var(--c-text); display:flex; align-items:center; gap:0.5rem; }
    .cart-count { background:var(--c-accent); color:#111; font-size:0.75rem; font-weight:800; border-radius:20px; padding:0.1rem 0.5rem; }
    .cart-toggle-hint { color:var(--c-muted); font-size:1.1rem; transition:transform 0.3s; }
    .cart-panel.open .cart-toggle-hint { transform: rotate(180deg); }

    .cart-body { overflow-y:auto; max-height:300px; padding:0.5rem 0; }
    @media (max-width: 991px) { .cart-body { max-height:35vh; } }

    .cart-empty { text-align:center; color:var(--c-muted); padding:2rem 1rem; font-size:0.9rem; }
    .cart-empty i { font-size:2rem; display:block; margin-bottom:0.5rem; }

    /* ── Items carrito ── REDISEÑADO con controles +/- ───── */
    .cart-item {
        display: flex; align-items: center; padding: 0.6rem 1rem;
        border-bottom: 1px solid var(--c-border); gap: 0.6rem;
        animation: slideIn 0.2s ease;
    }
    @keyframes slideIn {
        from { opacity:0; transform:translateX(-10px); }
        to   { opacity:1; transform:translateX(0); }
    }
    .cart-item-name  { flex:1; font-size:0.85rem; font-weight:500; color:var(--c-text); line-height:1.2; }
    .cart-item-price { font-size:0.78rem; color:var(--c-muted); }

    /* Controles de cantidad en el carrito */
    .qty-controls { display:flex; align-items:center; gap:0.25rem; }
    .qty-btn {
        background: var(--c-border); border: none; color: var(--c-text);
        border-radius: 6px; width:30px; height:30px;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:1.1rem; font-weight:700; transition:background 0.15s;
        -webkit-tap-highlight-color: transparent; flex-shrink:0;
    }
    .qty-btn:hover  { background: var(--c-accent); color:#111; }
    .qty-btn:active { transform: scale(0.92); }
    .qty-display { font-family:var(--font-display); font-size:1rem; font-weight:700; min-width:26px; text-align:center; color:var(--c-text); }

    .cart-item-subtotal { font-family:var(--font-display); font-size:0.95rem; font-weight:700; color:var(--c-accent); min-width:70px; text-align:right; }

    /* ── Footer carrito ──────────────────────────────────── */
    .cart-footer { padding:1rem; border-top:1px solid var(--c-border); }

    .cart-total-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem; }
    .cart-total-label  { font-size:0.9rem; color:var(--c-muted); font-weight:500; }
    .cart-total-amount { font-family:var(--font-display); font-size:1.7rem; font-weight:800; color:var(--c-accent); }

    /* Botones de pago */
    .pay-buttons { display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.4rem; margin-bottom:0.75rem; }
    .pay-btn {
        border:1.5px solid var(--c-border); background:none; border-radius:10px;
        padding:0.6rem 0.3rem; cursor:pointer;
        display:flex; flex-direction:column; align-items:center; gap:0.2rem;
        color:var(--c-muted); font-size:0.7rem; font-weight:600; transition:all 0.15s;
        -webkit-tap-highlight-color: transparent;
    }
    .pay-btn i { font-size:1.2rem; }
    .pay-btn.active.efectivo     { border-color:#2ecc71; color:#2ecc71; background:rgba(46,204,113,0.1); }
    .pay-btn.active.transferencia{ border-color:#3498db; color:#3498db; background:rgba(52,152,219,0.1); }
    .pay-btn.active.fiado        { border-color:#e84040; color:#e84040; background:rgba(232,64,64,0.1); }

    /* Selector cliente fiado */
    .cliente-select-wrapper { display:none; margin-bottom:0.75rem; animation:fadeIn 0.2s ease; }
    .cliente-select-wrapper.show { display:block; }

    /* ── Aviso Cuenta Genérica ───────────────────────────── */
    .cuenta-generica-hint {
        background: rgba(245,166,35,0.10);
        border: 1px solid rgba(245,166,35,0.3);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.78rem;
        color: var(--c-accent);
        margin-top: 0.4rem;
        display: none;
        gap: 0.4rem;
        align-items: flex-start;
        line-height: 1.4;
    }
    .cuenta-generica-hint.show { display: flex; }

    .cliente-select-wrapper select {
        width:100%; background:var(--c-bg); border:1.5px solid #e84040;
        border-radius:10px; color:var(--c-text); padding:0.65rem 0.75rem;
        font-size:0.9rem; outline:none;
    }

    .btn-confirmar {
        width:100%; background:var(--c-accent); color:#111; border:none; border-radius:12px;
        font-family:var(--font-display); font-size:1.25rem; font-weight:800; letter-spacing:0.04em;
        padding:0.85rem; cursor:pointer; transition:background 0.15s, transform 0.1s;
        display:flex; align-items:center; justify-content:center; gap:0.5rem;
    }
    .btn-confirmar:hover   { background:var(--c-accent-dk); }
    .btn-confirmar:active  { transform:scale(0.97); }
    .btn-confirmar:disabled{ background:var(--c-border); color:var(--c-muted); cursor:not-allowed; }

    .btn-limpiar {
        width:100%; background:none; border:1px solid var(--c-border); color:var(--c-muted);
        border-radius:10px; font-size:0.85rem; padding:0.5rem; cursor:pointer; margin-top:0.4rem;
        transition:border-color 0.15s, color 0.15s;
    }
    .btn-limpiar:hover { border-color:#e84040; color:#e84040; }

    /* ── Formulario cliente nuevo inline ────────────────── */
    .nuevo-cliente-toggle {
        background: none; border: none; color: var(--c-accent);
        font-size: 0.78rem; font-weight: 600; cursor: pointer;
        padding: 0.3rem 0; display: flex; align-items: center; gap: 0.3rem;
        margin-top: 0.4rem; width: 100%;
        transition: opacity 0.15s;
    }
    .nuevo-cliente-toggle:hover { opacity: 0.75; }

    .nuevo-cliente-form {
        display: none;
        margin-top: 0.5rem;
        background: rgba(46,204,113,0.05);
        border: 1.5px solid rgba(46,204,113,0.3);
        border-radius: 10px;
        padding: 0.75rem;
        animation: fadeIn 0.2s ease;
    }
    .nuevo-cliente-form.show { display: block; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:none; } }

    .nuevo-cliente-label {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #2ecc71; margin-bottom: 0.4rem; display: block;
    }
    .nuevo-cliente-input {
        width: 100%; background: var(--c-bg);
        border: 1.5px solid rgba(46,204,113,0.4);
        border-radius: 8px; color: var(--c-text);
        padding: 0.6rem 0.75rem; font-size: 0.9rem; outline: none;
        transition: border-color 0.15s;
    }
    .nuevo-cliente-input:focus { border-color: #2ecc71; }
    .nuevo-cliente-input::placeholder { color: var(--c-muted); }
    .nuevo-cliente-hint { font-size: 0.72rem; color: var(--c-muted); margin-top: 0.35rem; }
</style>
@endsection

@section('content')
<div class="pos-layout">

    {{-- ── Panel de productos ──────────────────────────── --}}
    <div>
        <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Buscar producto…" autocomplete="off" autocorrect="off">
        </div>

        <div class="products-grid" id="productsGrid">
            @forelse($products as $product)
                <div class="product-card {{ $product->stock == 0 ? 'sin-stock' : '' }}"
                     data-id="{{ $product->id }}"
                     data-nombre="{{ $product->nombre }}"
                     data-precio="{{ $product->precio_venta }}"
                     data-stock="{{ $product->stock }}"
                     onclick="agregarAlCarrito(this)">

                    @if($product->image_path)
                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->nombre }}" class="prod-img" loading="lazy">
                    @else
                        <span class="prod-emoji">🛍️</span>
                    @endif

                    <div class="prod-name">{{ $product->nombre }}</div>
                    <div class="prod-price">${{ number_format($product->precio_venta, 0, ',', '.') }}</div>
                    <div class="prod-stock {{ $product->stock <= 3 ? 'low' : '' }}">
                        @if($product->stock == 0) Sin stock
                        @elseif($product->stock <= 3) ⚠️ Quedan {{ $product->stock }}
                        @else Stock: {{ $product->stock }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-results">
                    <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                    No hay productos.<br>
                    <a href="{{ route('products.create') }}" style="color:var(--c-accent);">Agregar productos</a>
                </div>
            @endforelse

            <div class="no-results" id="noResults" style="display:none;">
                <i class="bi bi-search"></i> Sin resultados para "<span id="searchTerm"></span>"
            </div>
        </div>
    </div>

    {{-- ── Carrito ──────────────────────────────────────── --}}
    <div class="cart-panel" id="cartPanel">

        <div class="cart-header" onclick="toggleCart()">
            <div class="cart-title">
                <i class="bi bi-cart3"></i> Carrito
                <span class="cart-count" id="cartCount">0</span>
            </div>
            <i class="bi bi-chevron-up cart-toggle-hint d-lg-none"></i>
        </div>

        <div class="cart-body" id="cartBody">
            <div class="cart-empty" id="cartEmpty">
                <i class="bi bi-cart-x"></i>
                Tocá un producto para agregarlo
            </div>
        </div>

        <div class="cart-footer">
            <div class="cart-total-row">
                <span class="cart-total-label">TOTAL</span>
                <span class="cart-total-amount" id="cartTotal">$0</span>
            </div>

            <div class="pay-buttons">
                <button class="pay-btn efectivo active" onclick="seleccionarPago('efectivo')">
                    <i class="bi bi-cash-coin"></i> Efectivo
                </button>
                <button class="pay-btn transferencia" onclick="seleccionarPago('transferencia')">
                    <i class="bi bi-phone"></i> Transfer
                </button>
                <button class="pay-btn fiado" onclick="seleccionarPago('fiado')">
                    <i class="bi bi-book"></i> Fiado
                </button>
            </div>

            {{-- Selector cliente --}}
            <div class="cliente-select-wrapper" id="clienteWrapper">
                <select id="clienteSelect" onchange="onClienteChange(this)">
                    <option value="">— Seleccioná el cliente —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ $customer->nombre === 'Cuenta Genérica' ? 'data-generica=true' : '' }}>
                            {{ $customer->nombre }}
                            @if($customer->debeAlgo())
                                (Debe ${{ number_format($customer->saldo_deudor, 0, ',', '.') }})
                            @endif
                        </option>
                    @endforeach
                </select>

                {{-- Aviso cuando seleccionan Cuenta Genérica --}}
                <div class="cuenta-generica-hint" id="cuentaGenericaHint">
                    <i class="bi bi-info-circle-fill" style="flex-shrink:0; margin-top:1px;"></i>
                    <span>
                        Usá esto si el cliente no está cargado. Después entrá a
                        <strong>La Libreta</strong>, creá el cliente y editá esta orden
                        para asignarle la deuda correctamente.
                    </span>
                </div>

                {{-- Botón + formulario para crear cliente nuevo on-the-fly --}}
                <button type="button" class="nuevo-cliente-toggle" id="btnNuevoCliente"
                        onclick="toggleNuevoCliente()">
                    <i class="bi bi-person-plus-fill"></i> El cliente no está en la lista, agregar nuevo
                </button>

                <div class="nuevo-cliente-form" id="nuevoClienteForm">
                    <label class="nuevo-cliente-label">
                        <i class="bi bi-person-check"></i> Nombre del cliente nuevo
                    </label>
                    <input type="text" id="nuevoClienteNombre"
                           class="nuevo-cliente-input"
                           placeholder="Ej: Juan Pérez"
                           oninput="onNuevoClienteInput()"
                           autocomplete="off">
                    <div class="nuevo-cliente-hint">
                        Se va a crear automáticamente al confirmar la venta.
                    </div>
                </div>
            </div>

            <button class="btn-confirmar" id="btnConfirmar" onclick="confirmarVenta()" disabled>
                <i class="bi bi-check-lg"></i> CONFIRMAR VENTA
            </button>
            <button class="btn-limpiar" onclick="limpiarCarrito()">
                <i class="bi bi-trash"></i> Limpiar carrito
            </button>
        </div>
    </div>

</div>

{{-- Formulario oculto --}}
<form id="ventaForm" method="POST" action="{{ route('pos.store') }}" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="hidden" name="metodo_pago"        id="formMetodoPago"        value="efectivo">
    <input type="hidden" name="customer_id"        id="formCustomerId">
    <input type="hidden" name="nuevo_cliente_nombre" id="formNuevoClienteNombre">
    <div id="formItems"></div>
</form>
@endsection

@section('scripts')
<script>
// ── Estado ────────────────────────────────────────────────────
let carrito    = {};   // { id: { nombre, precio, cantidad, stock } }
let metodoPago = 'efectivo';
let cartOpen   = false;

// ── Agregar producto ──────────────────────────────────────────
function agregarAlCarrito(card) {
    const id     = card.dataset.id;
    const nombre = card.dataset.nombre;
    const precio = parseFloat(card.dataset.precio);
    const stock  = parseInt(card.dataset.stock);

    if (carrito[id]) {
        if (carrito[id].cantidad >= stock) {
            toast('⚠️ Stock máximo alcanzado', 'warning');
            return;
        }
        carrito[id].cantidad++;
    } else {
        carrito[id] = { nombre, precio, cantidad: 1, stock };
    }

    card.style.borderColor = 'var(--c-accent)';
    setTimeout(() => card.style.borderColor = '', 300);

    renderCarrito();
    actualizarBadgesGrid();

    if (!cartOpen && window.innerWidth < 992) {
        setTimeout(abrirCarrito, 250);
    }
}

// ── Cambiar cantidad desde el carrito ─────────────────────────
function cambiarCantidad(id, delta) {
    if (!carrito[id]) return;
    const nueva = carrito[id].cantidad + delta;
    if (nueva <= 0) {
        delete carrito[id];
    } else if (nueva > carrito[id].stock) {
        toast('⚠️ Sin más stock disponible', 'warning');
        return;
    } else {
        carrito[id].cantidad = nueva;
    }
    renderCarrito();
    actualizarBadgesGrid();
}

// ── Render del carrito ────────────────────────────────────────
// Nunca toca #cartEmpty del HTML — lo controla con display.
// Siempre reconstruye los items en #cartItems (div separado).
function renderCarrito() {
    const ids    = Object.keys(carrito);
    const empty  = document.getElementById('cartEmpty');
    let items    = document.getElementById('cartItems');

    // Crear contenedor de items la primera vez
    if (!items) {
        items = document.createElement('div');
        items.id = 'cartItems';
        // Insertarlo antes del empty div (que siempre queda en el DOM)
        empty.parentNode.insertBefore(items, empty);
    }

    if (ids.length === 0) {
        items.innerHTML = '';
        empty.style.display = 'block';
        document.getElementById('cartTotal').textContent = '$0';
        document.getElementById('cartCount').textContent = '0';
        document.getElementById('btnConfirmar').disabled = true;
        return;
    }

    empty.style.display = 'none';

    let html  = '';
    let total = 0;
    let count = 0;

    ids.forEach(id => {
        const item     = carrito[id];
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        count += item.cantidad;
        html += `
        <div class="cart-item">
            <div style="flex:1;min-width:0;">
                <div class="cart-item-name">${escHtml(item.nombre)}</div>
                <div class="cart-item-price">$${fmt(item.precio)} c/u</div>
            </div>
            <div class="qty-controls">
                <button class="qty-btn" onclick="cambiarCantidad('${id}',-1)">−</button>
                <span class="qty-display">${item.cantidad}</span>
                <button class="qty-btn" onclick="cambiarCantidad('${id}',1)">+</button>
            </div>
            <div class="cart-item-subtotal">$${fmt(subtotal)}</div>
        </div>`;
    });

    items.innerHTML = html;
    document.getElementById('cartTotal').textContent = '$' + fmt(total);
    document.getElementById('cartCount').textContent = count;
    document.getElementById('btnConfirmar').disabled = false;
}

// ── Badges de cantidad en la cuadrícula ───────────────────────
function actualizarBadgesGrid() {
    document.querySelectorAll('.product-card').forEach(card => {
        const id    = card.dataset.id;
        let   badge = card.querySelector('.badge-in-cart');
        if (carrito[id]) {
            if (!badge) {
                badge = document.createElement('div');
                badge.className = 'badge-in-cart';
                card.appendChild(badge);
            }
            badge.textContent = carrito[id].cantidad;
        } else if (badge) {
            badge.remove();
        }
    });
}

// ── Método de pago ────────────────────────────────────────────
function seleccionarPago(metodo) {
    metodoPago = metodo;
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.classList.toggle('active', btn.classList.contains(metodo));
    });
    const wrapper = document.getElementById('clienteWrapper');
    wrapper.classList.toggle('show', metodo === 'fiado');
    if (metodo !== 'fiado') {
        // Limpiar estado del panel fiado al cambiar método
        document.getElementById('cuentaGenericaHint').classList.remove('show');
        document.getElementById('nuevoClienteForm').classList.remove('show');
        document.getElementById('nuevoClienteNombre').value = '';
        document.getElementById('clienteSelect').value = '';
    }
}

// Cuando cambia el select de cliente existente
function onClienteChange(sel) {
    const opt      = sel.options[sel.selectedIndex];
    const esGen    = opt.dataset.generica === 'true' || opt.text.includes('Cuenta Genérica');
    document.getElementById('cuentaGenericaHint').classList.toggle('show', esGen);

    // Si elige un cliente del select, ocultar y limpiar el form de nuevo cliente
    if (sel.value) {
        document.getElementById('nuevoClienteForm').classList.remove('show');
        document.getElementById('nuevoClienteNombre').value = '';
        document.getElementById('btnNuevoCliente').innerHTML =
            '<i class="bi bi-person-plus-fill"></i> El cliente no está en la lista, agregar nuevo';
    }
}

// Toggle del formulario de cliente nuevo
function toggleNuevoCliente() {
    const form    = document.getElementById('nuevoClienteForm');
    const input   = document.getElementById('nuevoClienteNombre');
    const btn     = document.getElementById('btnNuevoCliente');
    const abriendo = !form.classList.contains('show');

    form.classList.toggle('show', abriendo);

    if (abriendo) {
        // Limpiar el select de clientes existentes
        document.getElementById('clienteSelect').value = '';
        document.getElementById('cuentaGenericaHint').classList.remove('show');
        btn.innerHTML = '<i class="bi bi-x-circle"></i> Cancelar';
        setTimeout(() => input.focus(), 150);
    } else {
        input.value = '';
        btn.innerHTML = '<i class="bi bi-person-plus-fill"></i> El cliente no está en la lista, agregar nuevo';
    }
}

// Cuando escriben el nombre del cliente nuevo
function onNuevoClienteInput() {
    // Nada por ahora — validación se hace en confirmarVenta
}

// ── Confirmar venta ───────────────────────────────────────────
function confirmarVenta() {
    const ids = Object.keys(carrito);
    if (ids.length === 0) return;

    if (metodoPago === 'fiado') {
        const clienteId      = document.getElementById('clienteSelect').value;
        const nuevoNombre    = document.getElementById('nuevoClienteNombre').value.trim();
        const formNuevo      = document.getElementById('nuevoClienteForm');
        const nuevoAbierto   = formNuevo.classList.contains('show');

        if (nuevoAbierto) {
            // Modo cliente nuevo
            if (!nuevoNombre) {
                document.getElementById('nuevoClienteNombre').focus();
                toast('⚠️ Escribí el nombre del cliente nuevo', 'warning');
                return;
            }
            document.getElementById('formCustomerId').value          = '';
            document.getElementById('formNuevoClienteNombre').value  = nuevoNombre;
        } else {
            // Modo cliente existente
            if (!clienteId) {
                toast('⚠️ Seleccioná el cliente o creá uno nuevo', 'warning');
                return;
            }
            document.getElementById('formCustomerId').value          = clienteId;
            document.getElementById('formNuevoClienteNombre').value  = '';
        }
    } else {
        document.getElementById('formCustomerId').value         = '';
        document.getElementById('formNuevoClienteNombre').value = '';
    }

    document.getElementById('formMetodoPago').value = metodoPago;

    const itemsDiv = document.getElementById('formItems');
    itemsDiv.innerHTML = '';
    ids.forEach((id, i) => {
        const item = carrito[id];
        itemsDiv.innerHTML += `
            <input type="hidden" name="items[${i}][product_id]" value="${id}">
            <input type="hidden" name="items[${i}][cantidad]"   value="${item.cantidad}">`;
    });

    const btn = document.getElementById('btnConfirmar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando…';
    document.getElementById('ventaForm').submit();
}

// ── Limpiar ───────────────────────────────────────────────────
function limpiarCarrito() {
    carrito = {};
    renderCarrito();
    actualizarBadgesGrid();
}

// ── Toggle carrito mobile ─────────────────────────────────────
function toggleCart() {
    if (window.innerWidth >= 992) return;
    cartOpen = !cartOpen;
    document.getElementById('cartPanel').classList.toggle('open', cartOpen);
}
function abrirCarrito() {
    if (window.innerWidth >= 992) return;
    cartOpen = true;
    document.getElementById('cartPanel').classList.add('open');
}

// ── Búsqueda ──────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.product-card').forEach(card => {
        const match = card.dataset.nombre.toLowerCase().includes(term);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noResults').style.display   = (!visible && term) ? 'block' : 'none';
    document.getElementById('searchTerm').textContent    = this.value;
});

// ── Helpers ───────────────────────────────────────────────────
function fmt(n) {
    return new Intl.NumberFormat('es-AR', { minimumFractionDigits:0, maximumFractionDigits:2 }).format(n);
}
function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function toast(msg, tipo = 'success') {
    const colors = {
        success: { bg:'#1a4731', color:'#2ecc71', border:'#2ecc71' },
        warning: { bg:'#3d3010', color:'#f5a623', border:'#f5a623' },
        danger:  { bg:'#3d1f1f', color:'#e84040', border:'#e84040' },
    };
    const c = colors[tipo] || colors.success;
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
