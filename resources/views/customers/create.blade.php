{{-- resources/views/customers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('content')
<div style="max-width:480px; margin:0 auto;">
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('customers.index') }}"
           style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> La Libreta
        </a>
        <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-top:0.3rem;">
            ➕ Nuevo <span style="color:var(--c-accent);">Cliente</span>
        </div>
    </div>

    <div class="pos-card" style="padding:1.5rem;">
        @if($errors->any())
        <div style="background:#3d1f1f; border-left:4px solid #e84040; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; color:#e84040; font-size:0.85rem;">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Nombre *
                </label>
                <input type="text" name="nombre" class="form-control"
                       style="background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none;"
                       value="{{ old('nombre') }}" placeholder="Nombre del cliente" required autofocus>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Teléfono
                </label>
                <input type="text" name="telefono"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none;"
                       value="{{ old('telefono') }}" placeholder="Ej: 3425-123456">
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Notas
                </label>
                <textarea name="notas" rows="2"
                          style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none; resize:vertical; font-family:var(--font-body);"
                          placeholder="Observaciones opcionales…">{{ old('notas') }}</textarea>
            </div>

            <div style="display:flex; gap:0.6rem;">
                <button type="submit" class="btn-accent" style="flex:1; border-radius:10px;">
                    <i class="bi bi-person-plus"></i> Agregar a la libreta
                </button>
                <a href="{{ route('customers.index') }}" class="btn-outline-ghost"
                   style="text-decoration:none; display:flex; align-items:center; gap:0.3rem;">
                    <i class="bi bi-x"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
