@extends('layouts.app')
@section('title', 'Nuevo Producto')

@section('content')

<div style="margin-bottom:1.25rem;">
    <a href="{{ route('products.index') }}"
       style="color:var(--c-muted); text-decoration:none; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.3rem;">
        <i class="bi bi-arrow-left"></i> Volver a productos
    </a>
    <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:800; color:var(--c-text); margin-top:0.3rem;">
        ➕ Nuevo <span style="color:var(--c-accent);">Producto</span>
    </div>
</div>

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('products._form', ['product' => new App\Models\Product()])
</form>

@endsection
