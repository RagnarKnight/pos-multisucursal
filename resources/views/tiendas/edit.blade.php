@extends('layouts.app')
@section('title', 'Configurar — ' . $tienda->nombre)
@section('content')
@include('tiendas._form', [
    'accion'  => route('tiendas.update', $tienda),
    'metodo'  => 'PUT',
])
@endsection
