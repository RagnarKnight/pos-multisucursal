@extends('layouts.app')
@section('title', 'Nueva tienda')
@section('content')
@include('tiendas._form', [
    'tienda' => null,
    'accion' => route('tiendas.store'),
    'metodo' => 'POST',
])
@endsection
