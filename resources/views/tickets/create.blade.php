@extends('layouts.app')

@section('titulo_modulo', 'Punto de Venta (POS)')
@section('subtitulo_modulo', 'Cajero: ' . Auth::user()->name)

@section('content')
<div class="container-fluid px-3 py-2">

    @livewire('pos-table')

</div>
@endsection

@push('scripts')
    @vite(['resources/js/pos.js'])
@endpush