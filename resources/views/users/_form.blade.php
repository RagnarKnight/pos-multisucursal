@php
    $editando        = isset($user) && $user !== null;
    $yo              = auth()->user();
    $rolesDisponibles = $rolesDisponibles ?? ['admin','empleado'];
    $iconosRol = [
        'superadmin' => ['icon'=>'🛡️', 'label'=>'Super Admin', 'desc'=>'Acceso total al sistema'],
        'admin'      => ['icon'=>'⚙️', 'label'=>'Admin',        'desc'=>'Gestiona su tienda'],
        'empleado'   => ['icon'=>'🛒', 'label'=>'Empleado',     'desc'=>'Solo vende'],
    ];
@endphp

<div style="max-width:500px; margin:0 auto;">

    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('users.index') }}"
           style="color:var(--c-muted);text-decoration:none;font-size:0.85rem;display:inline-flex;align-items:center;gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> Volver a usuarios
        </a>
    </div>

    <div style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;color:var(--c-text);margin-bottom:1.25rem;">
        👤 {!! $editando ? 'Editar usuario' : 'Nuevo <span style="color:var(--c-accent)">usuario</span>' !!}
    </div>

    <div style="background:var(--c-surface);border:1.5px solid var(--c-border);border-radius:16px;padding:1.5rem;">
        <form method="POST" action="{{ $accion }}">
            @csrf @method($metodo)

            {{-- Nombre --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $user?->name) }}" required autofocus
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                @error('name') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user?->email) }}" required
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                @error('email') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Rol --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.4rem;">Rol</label>
                <div style="display:grid;grid-template-columns:repeat({{ count($rolesDisponibles) }}, 1fr);gap:0.5rem;">
                    @foreach($rolesDisponibles as $rol)
                    @php $info = $iconosRol[$rol]; @endphp
                    <div class="rol-option" id="rol-{{ $rol }}"
                         onclick="selRol('{{ $rol }}')"
                         style="border:1.5px solid var(--c-border);border-radius:10px;padding:0.75rem;text-align:center;cursor:pointer;transition:all 0.15s;">
                        <div style="font-size:1.4rem;">{{ $info['icon'] }}</div>
                        <div style="font-weight:600;font-size:0.88rem;color:var(--c-text);">{{ $info['label'] }}</div>
                        <div style="font-size:0.68rem;color:var(--c-muted);">{{ $info['desc'] }}</div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="rol" id="rolInput" value="{{ old('rol', $user?->rol ?? $rolesDisponibles[count($rolesDisponibles)-1]) }}">
                @error('rol') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Tienda (solo si superadmin y el rol no es superadmin) --}}
            @if($yo->esSuperAdmin())
            <div style="margin-bottom:1rem;" id="tiendaField">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">Tienda asignada</label>
                <select name="tienda_id" id="tiendaSelect"
                        style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;">
                    <option value="">— Sin tienda (superadmin) —</option>
                    @foreach($tiendas as $t)
                    <option value="{{ $t->id }}" {{ old('tienda_id', $user?->tienda_id) == $t->id ? 'selected' : '' }}>
                        {{ $t->nombre }} @if($t->ciudad)— {{ $t->ciudad }}@endif
                    </option>
                    @endforeach
                </select>
                @error('tienda_id') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>
            @else
            {{-- Admin: la tienda es fija (la suya), campo oculto --}}
            <input type="hidden" name="tienda_id" value="{{ $yo->tienda_id }}">
            @endif

            {{-- Contraseña --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--c-muted);display:block;margin-bottom:0.35rem;">
                    Contraseña {{ $editando ? '<span style="font-weight:400;text-transform:none;">(vacío = no cambiar)</span>' : '' }}
                </label>
                <input type="password" name="password"
                       {{ $editando ? '' : 'required' }}
                       placeholder="Mínimo 8 caracteres"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;margin-bottom:0.4rem;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                <input type="password" name="password_confirmation"
                       placeholder="Repetir contraseña"
                       style="width:100%;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:10px;color:var(--c-text);padding:0.7rem 0.9rem;font-size:0.95rem;outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                @error('password') <div style="color:#e84040;font-size:0.78rem;margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-accent"
                    style="width:100%;border-radius:12px;padding:0.85rem;font-family:var(--font-display);font-size:1.1rem;font-weight:800;margin-top:0.5rem;">
                <i class="bi bi-check-lg"></i> {{ $editando ? 'Guardar cambios' : 'Crear usuario' }}
            </button>
        </form>
    </div>
</div>

<script>
const ROLES_DISP = @json($rolesDisponibles);

function selRol(rol) {
    document.getElementById('rolInput').value = rol;

    ROLES_DISP.forEach(r => {
        const el = document.getElementById('rol-' + r);
        if (!el) return;
        if (r === rol) {
            el.style.borderColor = 'var(--c-accent)';
            el.style.background  = 'rgba(245,166,35,0.08)';
        } else {
            el.style.borderColor = 'var(--c-border)';
            el.style.background  = 'none';
        }
    });

    // Ocultar tienda si el rol es superadmin
    const tiendaField = document.getElementById('tiendaField');
    if (tiendaField) {
        tiendaField.style.display = rol === 'superadmin' ? 'none' : 'block';
        const sel = document.getElementById('tiendaSelect');
        if (sel && rol === 'superadmin') sel.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const rolActual = document.getElementById('rolInput').value || ROLES_DISP[ROLES_DISP.length - 1];
    selRol(rolActual);
});
</script>
