<div class="user-card {{ $user->id === $yo->id ? 'es-yo' : '' }}">
    <div class="user-avatar avatar-{{ $user->rol }}">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>
    <div class="user-info">
        <div class="user-name">
            {{ $user->name }}
            @if($user->id === $yo->id) <span class="yo-badge">(vos)</span> @endif
        </div>
        <div class="user-email">{{ $user->email }}</div>
    </div>
    <span class="rol-badge rol-{{ $user->rol }}">{{ $user->rol }}</span>
    <div style="display:flex;gap:.3rem;">
        <a href="{{ route('users.edit', $user) }}" class="action-btn" title="Editar">
            <i class="bi bi-pencil"></i>
        </a>
        @if($user->id !== $yo->id)
        <form method="POST" action="{{ route('users.destroy', $user) }}"
              onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="action-btn danger" title="Eliminar">
                <i class="bi bi-trash"></i>
            </button>
        </form>
        @endif
    </div>
</div>
