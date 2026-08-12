@extends('layouts.app')

@section('titulo_modulo', 'Punto de Venta (POS)')
@section('subtitulo_modulo', 'Cajero: ' . Auth::user()->name)

@section('content')
<div class="container-fluid px-3 py-2">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Venta exitosa!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Hubo un problema con la venta!</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- AQUÍ SE INYECTA TODO EL POS -->
    @livewire('pos-table')

</div>
@endsection

@push('scripts')
    @vite(['resources/js/pos.js'])
@endpush