@extends('layouts.app')
@section('title', 'Editar Cliente')

@section('content')
<div style="max-width:480px; margin:0 auto;">
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('customers.show', $customer) }}"
           style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
            <i class="bi bi-arrow-left"></i> {{ $customer->nombre }}
        </a>
        <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-top:0.3rem;">
            ✏️ Editar <span style="color:var(--c-accent);">Cliente</span>
        </div>
    </div>

    <div class="pos-card" style="padding:1.5rem;">
        @if($errors->any())
        <div style="background:#3d1f1f; border-left:4px solid #e84040; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; color:#e84040; font-size:0.85rem;">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Nombre *
                </label>
                <input type="text" name="nombre"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none;"
                       value="{{ old('nombre', $customer->nombre) }}" required>
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Teléfono
                </label>
                <input type="text" name="telefono"
                       style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none;"
                       value="{{ old('telefono', $customer->telefono) }}">
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">
                    Notas
                </label>
                <textarea name="notas" rows="2"
                          style="width:100%; background:var(--c-bg); border:1.5px solid var(--c-border); border-radius:10px; color:var(--c-text); padding:0.7rem 0.9rem; font-size:1rem; outline:none; resize:vertical; font-family:var(--font-body);">{{ old('notas', $customer->notas) }}</textarea>
            </div>

            <div style="display:flex; gap:0.6rem;">
                <button type="submit" class="btn-accent" style="flex:1; border-radius:10px;">
                    <i class="bi bi-check-lg"></i> Guardar cambios
                </button>
                <a href="{{ route('customers.show', $customer) }}" class="btn-outline-ghost"
                   style="text-decoration:none; display:flex; align-items:center; gap:0.3rem;">
                    <i class="bi bi-x"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
