@php $editando = isset($tienda) && $tienda !== null; @endphp

<div style="max-width:540px; margin:0 auto;">

    <div style="margin-bottom:1.25rem;">
        <a href="{{ auth()->user()->esSuperAdmin() ? route('tiendas.index') : route('pos.index') }}"
           style="color:var(--c-muted);text-decoration:none;font-size:0.85rem;display:inline-flex;align-items:center;gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;color:var(--c-text);margin-bottom:1.25rem;">
        🏪 {{ $editando ? 'Configurar tienda' : 'Nueva <span style="color:var(--c-accent)">tienda</span>' }}
    </div>

    <form method="POST" action="{{ $accion }}" enctype="multipart/form-data">
        @csrf @method($metodo)

        {{-- Logo actual --}}
        @if($editando && $tienda->logoUrl())
        <div style="background:var(--c-surface);border:1.5px solid var(--c-border);border-radius:14px;padding:1rem 1.1rem;margin-bottom:1rem;display:flex;align-items:center;gap:1rem;">
            <img src="{{ $tienda->logoUrl() }}" alt="Logo actual"
                 style="height:60px;max-width:160px;object-fit:contain;border-radius:8px;background:var(--c-bg);padding:4px;">
            <div>
                <div style="font-size:0.8rem;color:var(--c-muted);margin-bottom:0.4rem;">Logo actual</div>
                <label style="display:flex;align-items:center;gap:0.4rem;cursor:pointer;font-size:0.82rem;color:#e84040;">
                    <input type="checkbox" name="borrar_logo" style="accent-color:#e84040;">
                    Eliminar logo
                </label>
            </div>
        </div>
        @endif

        <div style="background:var(--c-surface);border:1.5px solid var(--c-border);border-radius:16px;padding:1.5rem;margin-bottom:1rem;">
            <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);margin-bottom:1rem;">
                Datos de la tienda
            </div>

            {{-- Nombre --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Nombre del negocio <span style="color:#e84040">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre', $tienda?->nombre) }}"
                       required autofocus
                       placeholder="Ej: Kiosco La Esquina"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                @error('nombre') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Ciudad --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Ciudad
                </label>
                <input type="text" name="ciudad" value="{{ old('ciudad', $tienda?->ciudad) }}"
                       placeholder="Ej: Santa Fe"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
            </div>

            {{-- Dirección --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Dirección
                </label>
                <input type="text" name="direccion" value="{{ old('direccion', $tienda?->direccion) }}"
                       placeholder="Ej: Av. San Martín 1234"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
            </div>

            {{-- Teléfono --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Teléfono / WhatsApp
                </label>
                <input type="text" name="telefono" value="{{ old('telefono', $tienda?->telefono) }}"
                       placeholder="Ej: 3424001234"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
            </div>

            {{-- Logo --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Logo <span style="font-weight:400;text-transform:none;">(PNG/JPG, máx 2MB)</span>
                </label>
                <div style="border:2px dashed var(--c-border);border-radius:10px;padding:1rem;text-align:center;cursor:pointer;transition:border-color 0.15s;"
                     onclick="document.getElementById('logoInput').click()"
                     ondragover="this.style.borderColor='var(--c-accent)'"
                     ondragleave="this.style.borderColor='var(--c-border)'">
                    <div id="logoPreviewWrap" style="display:none;margin-bottom:0.5rem;">
                        <img id="logoPreview" src="" alt="Preview"
                             style="max-height:60px;max-width:180px;object-fit:contain;border-radius:6px;">
                    </div>
                    <div id="logoPlaceholder">
                        <i class="bi bi-image" style="font-size:1.5rem;color:var(--c-muted);"></i>
                        <div style="font-size:0.82rem;color:var(--c-muted);margin-top:0.3rem;">
                            Tocá para subir logo
                        </div>
                    </div>
                    <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;"
                           onchange="previewLogo(this)">
                </div>
                @error('logo') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn-accent"
                style="width:100%;border-radius:12px;padding:0.85rem;font-family:var(--font-display);font-size:1.1rem;font-weight:800;">
            <i class="bi bi-check-lg"></i> {{ $editando ? 'Guardar cambios' : 'Crear tienda' }}
        </button>
    </form>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreviewWrap').style.display = 'block';
            document.getElementById('logoPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
