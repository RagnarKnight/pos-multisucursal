@extends('layouts.app')
@section('title', 'Editar Producto')

@section('content')

<div style="margin-bottom:1.25rem;">
    <a href="{{ route('products.index') }}"
       style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
        <i class="bi bi-arrow-left"></i> Volver a productos
    </a>
    <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-top:0.3rem;">
        ✏️ Editar <span style="color:var(--c-accent);">{{ $product->nombre }}</span>
    </div>
</div>

<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('products._form', ['product' => $product])
</form>

@endsection
