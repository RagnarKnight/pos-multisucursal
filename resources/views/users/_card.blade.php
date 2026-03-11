@php
    $tieneVentas = $user->orders()->exists();
    $puedeEliminar = !$tieneVentas && $user->id !== $yo->id;
    $puedeToggle   = $user->id !== $yo->id;
@endphp

<div class="user-card {{ $user->id === $yo->id ? 'es-yo' : '' }} {{ !$user->activo ? 'inactivo' : '' }}">

    <div class="user-avatar avatar-{{ $user->rol }} {{ !$user->activo ? 'apagado' : '' }}">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>

    <div class="user-info">
        <div class="user-name">
            {{ $user->name }}
            @if($user->id === $yo->id) <span class="yo-badge">(vos)</span> @endif
            @if(!$user->activo)
                <span style="font-size:.68rem;background:rgba(232,64,64,.12);color:#e84040;border:1px solid rgba(232,64,64,.3);border-radius:5px;padding:.1rem .45rem;font-weight:700;letter-spacing:.04em;">INACTIVO</span>
            @endif
        </div>
        <div class="user-email">{{ $user->email }}</div>
        @if($tieneVentas)
            <div style="font-size:.7rem;color:var(--c-muted);margin-top:.15rem;">
                <i class="bi bi-receipt"></i> {{ $user->orders()->count() }} venta{{ $user->orders()->count() > 1 ? 's' : '' }} registrada{{ $user->orders()->count() > 1 ? 's' : '' }}
            </div>
        @endif
    </div>

    <span class="rol-badge rol-{{ $user->rol }}">{{ $user->rol }}</span>

    <div style="display:flex;gap:.3rem;align-items:center;">

        {{-- Editar --}}
        <a href="{{ route('users.edit', $user) }}" class="action-btn" title="Editar">
            <i class="bi bi-pencil"></i>
        </a>

        {{-- Toggle activo/inactivo --}}
        @if($puedeToggle)
        <form method="POST" action="{{ route('users.toggle-activo', $user) }}" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit"
                    class="action-btn {{ $user->activo ? 'btn-desactivar' : 'btn-activar' }}"
                    title="{{ $user->activo ? 'Desactivar — no podrá iniciar sesión' : 'Activar' }}"
                    onclick="return confirm('{{ $user->activo ? '¿Desactivar a ' . addslashes($user->name) . '? No podrá iniciar sesión.' : '¿Activar a ' . addslashes($user->name) . '?' }}')">
                <i class="bi bi-{{ $user->activo ? 'pause-circle' : 'play-circle' }}"></i>
            </button>
        </form>
        @endif

        {{-- Eliminar — solo si NO tiene ventas --}}
        @if($puedeEliminar)
        <form method="POST" action="{{ route('users.destroy', $user) }}"
              onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}? Esta acción no se puede deshacer.')"
              style="margin:0;">
            @csrf @method('DELETE')
            <button type="submit" class="action-btn danger" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </form>
        @elseif($tieneVentas && $user->id !== $yo->id)
        {{-- Ícono bloqueado con tooltip explicativo --}}
        <span class="action-btn"
              title="Tiene ventas registradas — usá desactivar en su lugar"
              style="cursor:not-allowed;opacity:.35;">
            <i class="bi bi-trash"></i>
        </span>
        @endif

    </div>
</div>
