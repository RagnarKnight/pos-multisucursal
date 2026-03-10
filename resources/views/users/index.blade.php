@extends('layouts.app')
@section('title', 'Usuarios')

@section('extra-css')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem; }
    .page-title  { font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); }
    .page-title span { color:var(--c-accent); }

    .user-card {
        background: var(--c-surface); border: 1.5px solid var(--c-border);
        border-radius: 14px; padding: 1rem 1.1rem; margin-bottom: 0.5rem;
        display: flex; align-items: center; gap: 1rem; transition: border-color 0.15s;
    }
    .user-card:hover { border-color: var(--c-accent); }
    .user-card.es-yo { border-left: 4px solid var(--c-accent); }

    .user-avatar {
        width:44px; height:44px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        font-family:var(--font-display); font-size:1.1rem; font-weight:800; flex-shrink:0;
    }
    .avatar-admin    { background:rgba(245,166,35,0.15); color:var(--c-accent); }
    .avatar-empleado { background:var(--c-border); color:var(--c-muted); }

    .user-info  { flex:1; min-width:0; }
    .user-name  { font-weight:600; font-size:1rem; color:var(--c-text); }
    .user-email { font-size:0.8rem; color:var(--c-muted); }

    .rol-badge    { font-size:0.7rem; font-weight:700; border-radius:6px; padding:0.2rem 0.55rem; text-transform:uppercase; letter-spacing:0.06em; }
    .rol-admin    { background:rgba(245,166,35,0.15); color:var(--c-accent); }
    .rol-empleado { background:var(--c-border); color:var(--c-muted); }
    .yo-badge     { font-size:0.7rem; color:var(--c-muted); font-style:italic; }

    .action-btn { background:none; border:1.5px solid var(--c-border); color:var(--c-muted); border-radius:8px; padding:0.35rem 0.55rem; cursor:pointer; font-size:0.9rem; transition:all 0.15s; text-decoration:none; display:inline-flex; align-items:center; }
    .action-btn:hover        { color:var(--c-accent); border-color:var(--c-accent); }
    .action-btn.danger:hover { color:#e84040; border-color:#e84040; }
</style>
@endsection

@section('content')

<div class="page-header" style="margin-bottom:10px;" >
    <div class="page-title">👤 <span>Usuarios</span></div>
    <a href="{{ route('users.create') }}" class="btn-accent"
       style="font-size:0.95rem; padding:0.6rem 1.1rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; border-radius:10px;">
        <i class="bi bi-plus-lg"></i> Nuevo usuario
    </a>
</div>

@foreach($users as $user)
<div class="user-card {{ $user->id === auth()->id() ? 'es-yo' : '' }}">
    <div class="user-avatar {{ $user->esAdmin() ? 'avatar-admin' : 'avatar-empleado' }}">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>
    <div class="user-info">
        <div class="user-name">
            {{ $user->name }}
            @if($user->id === auth()->id()) <span class="yo-badge">(vos)</span> @endif
        </div>
        <div class="user-email">{{ $user->email }}</div>
    </div>
    <span class="rol-badge rol-{{ $user->rol }}">{{ $user->rol }}</span>
    <div style="display:flex; gap:0.35rem;">
        <a href="{{ route('users.edit', $user) }}" class="action-btn" title="Editar">
            <i class="bi bi-pencil"></i>
        </a>
        @if($user->id !== auth()->id())
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
@endforeach

@endsection
