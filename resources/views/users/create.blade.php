@extends('layouts.app')
@section('title', 'Nuevo Usuario')
@section('content')
@include('users._form', [
    'user'             => null,
    'accion'           => route('users.store'),
    'metodo'           => 'POST',
])
@endsection
