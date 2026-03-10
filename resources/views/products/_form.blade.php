{{--
    Partial reutilizable para crear y editar productos.
    Variables esperadas: $product (puede ser new Product() vacío)
--}}
<style>
    .form-card {
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 16px;
        padding: 1.5rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-section-title {
        font-family: var(--font-display);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: 0.75rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid var(--c-border);
    }

    .form-group { margin-bottom: 1.1rem; }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--c-muted);
        margin-bottom: 0.35rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .form-input {
        width: 100%;
        background: var(--c-bg);
        border: 1.5px solid var(--c-border);
        border-radius: 10px;
        color: var(--c-text);
        font-size: 1rem;
        padding: 0.7rem 0.9rem;
        outline: none;
        transition: border-color 0.2s;
        font-family: var(--font-body);
    }
    .form-input:focus  { border-color: var(--c-accent); }
    .form-input.is-invalid { border-color: #e84040; }
    .form-input::placeholder { color: var(--c-muted); }

    textarea.form-input { resize: vertical; min-height: 80px; }

    .form-hint {
        font-size: 0.75rem;
        color: var(--c-muted);
        margin-top: 0.25rem;
    }
    .form-error {
        font-size: 0.78rem;
        color: #e84040;
        margin-top: 0.25rem;
    }

    /* ── Grid de precios y stock ─────────────────────────── */
    .price-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    @media (min-width: 480px) {
        .price-grid { grid-template-columns: 1fr 1fr 1fr; }
    }

    /* Prefijo $ en inputs de precio */
    .input-prefix {
        position: relative;
    }
    .input-prefix span {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--c-muted);
        font-weight: 600;
        pointer-events: none;
    }
    .input-prefix .form-input { padding-left: 1.7rem; }

    /* ── Margen calculado en tiempo real ─────────────────── */
    .margen-live {
        background: rgba(46,204,113,0.1);
        border: 1px solid rgba(46,204,113,0.3);
        border-radius: 8px;
        padding: 0.6rem 0.9rem;
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: #2ecc71;
        text-align: center;
        margin-top: 0.5rem;
    }
    .margen-live.negativo { color: #e84040; background: rgba(232,64,64,0.1); border-color: rgba(232,64,64,0.3); }

    /* ── Upload de imagen ────────────────────────────────── */
    .image-upload-area {
        border: 2px dashed var(--c-border);
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .image-upload-area:hover { border-color: var(--c-accent); background: rgba(245,166,35,0.04); }
    .image-upload-area input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .image-preview {
        max-width: 100px;
        max-height: 100px;
        border-radius: 10px;
        object-fit: cover;
        margin-bottom: 0.5rem;
    }
    .upload-hint { font-size: 0.82rem; color: var(--c-muted); }

    /* ── Toggle activo ───────────────────────────────────── */
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .toggle-switch {
        position: relative;
        width: 48px; height: 26px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0;
        background: var(--c-border);
        border-radius: 26px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .toggle-slider:before {
        content: '';
        position: absolute;
        left: 3px; bottom: 3px;
        width: 20px; height: 20px;
        background: var(--c-muted);
        border-radius: 50%;
        transition: transform 0.2s, background 0.2s;
    }
    .toggle-switch input:checked + .toggle-slider { background: rgba(46,204,113,0.3); }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); background: #2ecc71; }

    /* ── Botones de acción ───────────────────────────────── */
    .form-actions {
        display: flex;
        gap: 0.6rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--c-border);
    }
</style>

<div class="form-card">

    {{-- Errores de validación --}}
    @if($errors->any())
    <div style="background:#3d1f1f; border-left:4px solid #e84040; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; color:#e84040; font-size:0.85rem;">
        <strong><i class="bi bi-exclamation-triangle-fill"></i> Corregí los siguientes campos:</strong>
        <ul style="margin:0.4rem 0 0 1rem; padding:0;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Sección: Datos básicos ──────────────────────── --}}
    <div class="form-section-title">Datos del producto</div>

    <div class="form-group">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
               value="{{ old('nombre', $product->nombre) }}"
               placeholder="Ej: Coca Cola 2.25L" required autofocus>
        @error('nombre') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-input"
                  placeholder="Descripción opcional…">{{ old('descripcion', $product->descripcion) }}</textarea>
    </div>

    {{-- ── Sección: Precios y stock ────────────────────── --}}
    <div class="form-section-title mt-3">Precios y stock</div>

    <div class="price-grid">
        <div class="form-group">
            <label class="form-label">Precio costo</label>
            <div class="input-prefix">
                <span>$</span>
                <input type="number" name="precio_costo" id="precioCosto"
                       class="form-input {{ $errors->has('precio_costo') ? 'is-invalid' : '' }}"
                       value="{{ old('precio_costo', $product->precio_costo ?? 0) }}"
                       step="0.01" min="0" oninput="calcularMargen()">
            </div>
            @error('precio_costo') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Precio venta *</label>
            <div class="input-prefix">
                <span>$</span>
                <input type="number" name="precio_venta" id="precioVenta"
                       class="form-input {{ $errors->has('precio_venta') ? 'is-invalid' : '' }}"
                       value="{{ old('precio_venta', $product->precio_venta ?? '') }}"
                       step="0.01" min="0" required oninput="calcularMargen()">
            </div>
            @error('precio_venta') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Stock actual</label>
            <input type="number" name="stock"
                   class="form-input {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                   value="{{ old('stock', $product->stock ?? 0) }}"
                   min="0" step="1">
            @error('stock') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Margen en tiempo real --}}
    <div class="margen-live" id="margenLive">
        Margen: — %
    </div>

    {{-- ── Sección: Imagen ─────────────────────────────── --}}
    <div class="form-section-title mt-3">Imagen del producto</div>

    <div class="form-group">
        <div class="image-upload-area" id="uploadArea">
            <input type="file" name="imagen" accept="image/*"
                   onchange="previewImage(this)" id="imagenInput">

            @if($product->image_path ?? false)
                <img src="{{ Storage::url($product->image_path) }}"
                     class="image-preview" id="imagePreview">
                <div class="upload-hint">Toca para cambiar la imagen</div>
            @else
                <img src="" class="image-preview" id="imagePreview" style="display:none;">
                <i class="bi bi-image" style="font-size:2rem; color:var(--c-muted); display:block; margin-bottom:0.4rem;"></i>
                <div class="upload-hint">Toca para subir foto del producto<br><small>JPG, PNG — máx 2MB</small></div>
            @endif
        </div>
        <div class="form-hint">Se redimensiona automáticamente. Usá fotos cuadradas para mejor resultado.</div>
    </div>

    {{-- ── Sección: Estado ─────────────────────────────── --}}
    <div class="form-section-title mt-3">Estado</div>

    <div class="toggle-wrapper">
        <label class="toggle-switch">
            <input type="checkbox" name="activo" value="1"
                   {{ old('activo', $product->activo ?? true) ? 'checked' : '' }}>
            <span class="toggle-slider"></span>
        </label>
        <span style="font-size:0.9rem; color:var(--c-text);">Producto activo (visible en el POS)</span>
    </div>

    {{-- ── Botones ──────────────────────────────────────── --}}
    <div class="form-actions">
        <button type="submit" class="btn-accent" style="flex:1; border-radius:10px;">
            <i class="bi bi-check-lg"></i>
            {{ isset($product->id) ? 'Guardar cambios' : 'Crear producto' }}
        </button>
        <a href="{{ route('products.index') }}" class="btn-outline-ghost"
           style="display:flex; align-items:center; gap:0.4rem; text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

</div>

<script>
function calcularMargen() {
    const costo  = parseFloat(document.getElementById('precioCosto').value) || 0;
    const venta  = parseFloat(document.getElementById('precioVenta').value) || 0;
    const el     = document.getElementById('margenLive');

    if (costo <= 0 || venta <= 0) {
        el.textContent = 'Margen: — %';
        el.className = 'margen-live';
        return;
    }

    const margen = ((venta - costo) / costo) * 100;
    el.textContent = `Margen: ${margen >= 0 ? '+' : ''}${margen.toFixed(1)}%  (ganás $${(venta - costo).toFixed(2)} por unidad)`;
    el.className   = 'margen-live' + (margen < 0 ? ' negativo' : '');
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('imagePreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Calcular margen inicial si hay valores
document.addEventListener('DOMContentLoaded', calcularMargen);
</script>
