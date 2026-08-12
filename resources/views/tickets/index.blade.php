@extends('layouts.app')

@section('titulo_modulo', 'Gestión de Tickets y Pedidos')
@section('subtitulo_modulo', 'Administra el estado de las ventas y reservas de los clientes')

@section('content')

    <!-- ========================================== -->
    <!-- WIDGET DE CONTROL DE CAJA INTEGRADO        -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            @if (session('success'))
                <div class="alert alert-success fw-bold mb-3">{{ session('success') }}</div>
            @endif
            @if ($errors->has('error'))
                <div class="alert alert-danger fw-bold mb-3">{{ $errors->first('error') }}</div>
            @endif

            @if (!$turnoAbierto)
                <!-- CASO A: NO HAY TURNO ABIERTO (Muestra formulario de apertura con campo editable) -->
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="text-dark fw-bold mb-1"><i class="fas fa-cash-register text-primary me-2"></i> Apertura de Caja</h5>
                        <p class="text-muted small mb-0">Ingresa el monto de dinero base en efectivo para iniciar el turno.</p>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('caja.abrir') }}" method="POST" class="d-flex justify-content-md-end align-items-center gap-2">
                            @csrf
                            <div class="input-group style-input-group" style="max-width: 200px;">
                                <span class="input-group-text bg-white fw-bold">$</span>
                                <input type="number" step="0.01" class="form-control @error('monto_inicial') is-invalid @enderror" 
                                       name="monto_inicial" value="{{ old('monto_inicial', '20.00') }}" placeholder="Monto Inicial" required>
                            </div>
                            <button type="submit" class="btn btn-success text-nowrap shadow-sm fw-bold">
                                <i class="fas fa-box-open me-1"></i> Abrir Caja
                            </button>
                        </form>
                        @error('monto_inicial')
                            <div class="text-danger small fw-bold text-end mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @else
                <!-- CASO B: TURNO ABIERTO (Tarjetas Dashboard y Botón que abre Modal de Cierre) -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="text-dark fw-bold mb-0"><i class="fas fa-cash-register text-success me-2"></i> Estado de Caja Actual</h5>
                        <small class="text-muted">Turno abierto el: {{ $turnoAbierto->fecha_apertura->format('d/m/Y h:i A') }}</small>
                    </div>
                    <!-- Botón que activa el Modal de Cierre -->
                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">
                        <i class="fas fa-lock me-1"></i> Cerrar Caja
                    </button>
                </div>

                <!-- Tarjetas de Métricas en Vivo -->
                <div class="row row-cols-1 row-cols-md-5 g-3 text-start">
                    <!-- 1. Fondo Inicial -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 5px solid #6c757d !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Fondo Inicial</span>
                                    <h4 class="text-dark mb-0 mt-1">${{ number_format($turnoAbierto->monto_inicial, 2) }}</h4>
                                </div>
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Ventas POS Efectivo -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 5px solid #0d6efd !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">POS (Efectivo)</span>
                                    <h4 class="text-dark mb-0 mt-1">+ ${{ number_format($turnoAbierto->total_efectivo, 2) }}</h4>
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-cash-register"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Transferencias Web -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 5px solid #0dcaf0 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Web (Transferencias)</span>
                                    <h4 class="text-dark mb-0 mt-1">+ ${{ number_format($turnoAbierto->total_transferencias, 2) }}</h4>
                                </div>
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TOTAL VENTAS DIARIAS -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light" style="border-left: 5px solid #ffc107 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Ventas del Turno</span>
                                    <h4 class="text-dark mb-0 mt-1 fw-bold">${{ number_format($turnoAbierto->total_efectivo + $turnoAbierto->total_transferencias, 2) }}</h4>
                                </div>
                                <div class="bg-warning bg-opacity-25 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Total Físico Esperado en Gaveta -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100" style="border-left: 5px solid #198754 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-success small text-uppercase fw-bold">Físico en Caja</span>
                                    <h4 class="text-success mb-0 mt-1 fw-bold">${{ number_format($turnoAbierto->monto_inicial + $turnoAbierto->total_efectivo, 2) }}</h4>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               <!-- MODAL PARA CERRAR Y REALIZAR EL ARQUEO DE CAJA -->
                <div class="modal fade" id="modalCerrarCaja" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <form action="{{ route('caja.cerrar') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            @csrf
                            <div class="modal-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-calculator text-danger me-2"></i> Arqueo y Cierre de Caja</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            
                            <div class="modal-body px-4">
                                <!-- Tarjetas de Dinero Esperado (Sistema) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="alert alert-success d-flex flex-column justify-content-center mb-0 h-100 border-0 shadow-sm py-2">
                                            <span class="small text-uppercase fw-bold text-success mb-1">Efectivo en Gaveta</span>
                                            <h4 class="mb-0 fw-bold text-success">${{ number_format($turnoAbierto->monto_inicial + $turnoAbierto->total_efectivo, 2) }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info d-flex flex-column justify-content-center mb-0 h-100 border-0 shadow-sm py-2">
                                            <span class="small text-uppercase fw-bold text-info mb-1">Transferencias (Banco)</span>
                                            <h4 class="mb-0 fw-bold text-info">${{ number_format($turnoAbierto->total_transferencias, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inputs de Declaración (Cajero) -->
                                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Declaración del Cajero</h6>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">EFECTIVO FÍSICO CONTADO *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold">$</span>
                                            <input type="number" step="0.01" class="form-control input-arqueo @error('monto_real') is-invalid @enderror" 
                                                   name="monto_real" id="montoFisico" value="{{ old('monto_real') }}" placeholder="0.00" required>
                                        </div>
                                        @error('monto_real')
                                            <div class="text-danger small fw-bold mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">MONTO EN CUENTA BANCARIA *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold">$</span>
                                            <input type="number" step="0.01" class="form-control input-arqueo @error('transferencias_real') is-invalid @enderror" 
                                                   name="transferencias_real" id="montoTransferencias" value="{{ old('transferencias_real') }}" placeholder="0.00" required>
                                        </div>
                                        @error('transferencias_real')
                                            <div class="text-danger small fw-bold mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Totalizador Automático -->
                                <div class="mb-4 bg-light p-3 rounded-3 text-end border">
                                    <span class="fw-bold text-muted me-2">TOTAL DECLARADO:</span>
                                    <span class="fs-4 fw-bold text-dark" id="totalDeclarado">$0.00</span>
                                </div>

                                <!-- Campo: Observaciones -->
                                <div class="mb-1">
                                    <label class="form-label fw-bold">OBSERVACIONES / NOVEDADES</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              name="observaciones" rows="2" placeholder="Si hay sobrantes o faltantes, indica la razón aquí...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="modal-footer bg-white border-top-0 px-4 pb-4 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger fw-bold px-4">
                                    <i class="fas fa-lock me-1"></i> Confirmar Cierre
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
    <!-- ========================================== -->

    <!-- INYECCIÓN DEL COMPONENTE REACTIVO -->
    @livewire('tickets-table')

@endsection

@push('scripts')
    <!-- Reabrir Modal de Cierre de Caja en caso de error de validación -->
    @if ($errors->has('monto_real') || $errors->has('observaciones'))
        <button type="button" id="btnAutoOpenCierre" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja" class="d-none"></button>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    document.getElementById('btnAutoOpenCierre').click();
                }, 150);
            });
        </script>
    @endif

    <script>
        // Sumatoria en vivo del arqueo de caja
        document.addEventListener("DOMContentLoaded", function() {
            const inputEfectivo = document.getElementById('montoFisico');
            const inputTransferencias = document.getElementById('montoTransferencias');
            const totalDeclarado = document.getElementById('totalDeclarado');

            function calcularTotal() {
                const efectivo = parseFloat(inputEfectivo.value) || 0;
                const transferencias = parseFloat(inputTransferencias.value) || 0;
                const suma = efectivo + transferencias;
                totalDeclarado.textContent = '$' + suma.toFixed(2);
            }

            if(inputEfectivo && inputTransferencias) {
                inputEfectivo.addEventListener('input', calcularTotal);
                inputTransferencias.addEventListener('input', calcularTotal);
                calcularTotal(); // Llamada inicial por si hay un old()
            }
        });
    </script>
@endpush