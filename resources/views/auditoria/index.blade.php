@extends('layouts.app') 

@section('titulo_modulo', 'Auditoría del Sistema')
@section('subtitulo_modulo', 'Registro de movimientos, modificaciones y usuarios')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-sm bg-white">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Fecha y Hora</th>
                        <th scope="col">Usuario Responsable</th>
                        <th scope="col">Módulo</th>
                        <th scope="col">Acción</th>
                        <th scope="col">Detalles del Cambio (Antes ➡️ Después)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap text-muted">
                                {{ $log->created_at->format('d/m/Y h:i A') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary shadow-sm fs-6">
                                    <i class="fas fa-user-shield me-1"></i> 
                                    {{ $log->causer ? $log->causer->name : 'Sistema/Desconocido' }}
                                </span>
                            </td>
                            <td class="text-uppercase fw-bold text-muted">
                                {{ $log->log_name }}
                            </td>
                            <td>
                                @if($log->event == 'created') 
                                    <span class="badge bg-success">Creación</span>
                                @elseif($log->event == 'updated') 
                                    <span class="badge bg-warning text-dark">Modificación</span>
                                @elseif($log->event == 'deleted') 
                                    <span class="badge bg-danger">Eliminación</span>
                                @else 
                                    <span class="badge bg-info">{{ $log->event }}</span>
                                @endif
                            </td>
                            <td style="min-width: 300px;">
                                {{-- MAGIA DE SPATIE: Verificamos si hay datos viejos y nuevos para compararlos --}}
                                @if($log->properties->has('old') && $log->properties->has('attributes'))
                                    <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                        @foreach($log->properties['attributes'] as $key => $newValue)
                                            @php $oldValue = $log->properties['old'][$key] ?? 'N/A'; @endphp
                                            
                                            {{-- Solo mostramos si el valor realmente cambió --}}
                                            @if($oldValue != $newValue)
                                                <li class="mb-1">
                                                    <strong class="text-dark">{{ ucfirst($key) }}:</strong>
                                                    <span class="text-danger text-decoration-line-through mx-1">{{ $oldValue }}</span>
                                                    ➡️ 
                                                    <span class="text-success fw-bold ms-1">{{ $newValue }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @elseif($log->properties->has('attributes'))
                                    <ul class="list-unstyled mb-0 text-success" style="font-size: 0.85rem;">
                                        @foreach($log->properties['attributes'] as $key => $value)
                                            <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Sin detalles registrados</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-clipboard-list fs-1 mb-3 text-light"></i><br>
                                No hay registros de actividad todavía.<br>
                                ¡Ve a modificar el inventario o cambiar el estado de un ticket para ver la magia!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>

    </div>
</div>
@endsection