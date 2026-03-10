@php $editando = isset($user) && $user !== null; @endphp

<div style="max-width:480px; margin:0 auto;">

    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('users.index') }}"
           style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> Volver a usuarios
        </a>
    </div>

    <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-bottom:1.25rem;">
        👤 {!! $editando ? 'Editar usuario' : 'Nuevo <span style="color:var(--c-accent)">usuario</span>' !!}
    </div>

    <div style="background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:16px; padding:1.5rem;">
        <form method="POST" action="{{ $accion }}">
            @csrf @method($metodo)

            {{-- Nombre --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-muted); display:block; margin-bottom:0.35rem;">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $user?->name) }}"
                       class="field-input" required autofocus
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:0.95rem; outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'">
                @error('name') <div style="color:#e84040; font-size:0.78rem; margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-muted); display:block; margin-bottom:0.35rem;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user?->email) }}"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:0.95rem; outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'" required>
                @error('email') <div style="color:#e84040; font-size:0.78rem; margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Rol --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-muted); display:block; margin-bottom:0.35rem;">Rol</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="rol" value="empleado"
                               {{ old('rol', $user?->rol ?? 'empleado') === 'empleado' ? 'checked' : '' }}
                               style="display:none;" onchange="resaltarRol()">
                        <div class="rol-option" id="rol-empleado"
                             onclick="document.querySelector('[value=empleado]').checked=true; resaltarRol()"
                             style="border:1.5px solid var(--c-border); border-radius:10px; padding:0.75rem; text-align:center; transition:all 0.15s;">
                            <div style="font-size:1.5rem;">🛒</div>
                            <div style="font-weight:600; font-size:0.9rem; color:var(--c-text);">Empleado</div>
                            <div style="font-size:0.72rem; color:var(--c-muted);">Vende, ve clientes</div>
                        </div>
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio" name="rol" value="admin"
                               {{ old('rol', $user?->rol) === 'admin' ? 'checked' : '' }}
                               style="display:none;" onchange="resaltarRol()">
                        <div class="rol-option" id="rol-admin"
                             onclick="document.querySelector('[value=admin]').checked=true; resaltarRol()"
                             style="border:1.5px solid var(--c-border); border-radius:10px; padding:0.75rem; text-align:center; transition:all 0.15s;">
                            <div style="font-size:1.5rem;">⚙️</div>
                            <div style="font-weight:600; font-size:0.9rem; color:var(--c-text);">Admin</div>
                            <div style="font-size:0.72rem; color:var(--c-muted);">Acceso total</div>
                        </div>
                    </label>
                </div>
                @error('rol') <div style="color:#e84040; font-size:0.78rem; margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            {{-- Contraseña --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-muted); display:block; margin-bottom:0.35rem;">
                    Contraseña {{ $editando ? '(dejá vacío para no cambiar)' : '' }}
                </label>
                <input type="password" name="password"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:0.95rem; outline:none; margin-bottom:0.4rem;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'"
                       {{ $editando ? '' : 'required' }}
                       placeholder="Mínimo 8 caracteres">
                <input type="password" name="password_confirmation"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:0.95rem; outline:none;"
                       onfocus="this.style.borderColor='var(--c-accent)'"
                       onblur="this.style.borderColor='var(--c-border)'"
                       placeholder="Repetir contraseña">
                @error('password') <div style="color:#e84040; font-size:0.78rem; margin-top:0.3rem;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-accent"
                    style="width:100%; border-radius:12px; padding:0.85rem; font-family:var(--font-display); font-size:1.1rem; font-weight:800; margin-top:0.5rem;">
                <i class="bi bi-check-lg"></i> {{ $editando ? 'Guardar cambios' : 'Crear usuario' }}
            </button>
        </form>
    </div>
</div>

<script>
function resaltarRol() {
    ['admin','empleado'].forEach(rol => {
        const radio = document.querySelector(`[value="${rol}"]`);
        const div   = document.getElementById('rol-' + rol);
        if (radio.checked) {
            div.style.borderColor = 'var(--c-accent)';
            div.style.background  = 'rgba(245,166,35,0.07)';
        } else {
            div.style.borderColor = 'var(--c-border)';
            div.style.background  = 'none';
        }
    });
}
document.addEventListener('DOMContentLoaded', resaltarRol);
</script>
