@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
@include('users._form', [
    'accion' => route('users.update', $user),
    'metodo' => 'PUT',
])
@endsection
