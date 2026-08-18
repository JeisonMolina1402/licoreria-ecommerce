@extends('layouts.app') 

@section('titulo_modulo', 'Auditoría del Sistema')
@section('subtitulo_modulo', 'Registro de movimientos, modificaciones y usuarios')

@section('content')
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body p-4 bg-light">
        
        <!-- COMPONENTE LIVEWIRE: Aquí se inyecta la tabla interactiva y los filtros -->
        @livewire('auditoria-table')

    </div>
</div>
@endsection