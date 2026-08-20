let chartDonaInstance = null;
let chartBarrasInstance = null;
let chartVendedoresInstance = null;
let chartUsuariosInstance = null; 

document.addEventListener('DOMContentLoaded', function() {
    
    if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels);
    }

    const paletaColores = ['#0d6efd', '#ffc107', '#20c997', '#dc3545', '#6f42c1', '#fd7e14', '#198754', '#0dcaf0'];

    // --- 1. GRÁFICO DE DONA (Limpio, sin etiquetas internas) ---
    const canvasDona = document.getElementById('graficoCategorias');
    if (canvasDona) {
        const ctxDona = canvasDona.getContext('2d');
        const labels = JSON.parse(canvasDona.dataset.nombres || '[]');
        const data = JSON.parse(canvasDona.dataset.cantidades || '[]');

        chartDonaInstance = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: paletaColores,
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, font: { family: "'Nunito', sans-serif", size: 12 }, usePointStyle: true }
                    },
                    // Apagamos las etiquetas por completo para este gráfico
                    datalabels: { display: false }
                },
                cutout: '65%'
            }
        });
    }

    // --- 2. GRÁFICO DE BARRAS DINÁMICO ---
    const canvasBarras = document.getElementById('graficoBarras');
    if (canvasBarras) {
        const ctxBarras = canvasBarras.getContext('2d');
        const etiquetas = JSON.parse(canvasBarras.dataset.etiquetas || '[]');
        const ventas = JSON.parse(canvasBarras.dataset.ventas || '[]');
        const ganancias = JSON.parse(canvasBarras.dataset.ganancias || '[]');
        const gastos = JSON.parse(canvasBarras.dataset.gastos || '[]');

        chartBarrasInstance = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [
                    { label: 'Ingresos (Ventas Brutas)', data: ventas, backgroundColor: '#0d6efd', borderRadius: 4 },
                    { label: 'Costos (Gastos de Inventario)', data: gastos, backgroundColor: '#dc3545', borderRadius: 4 },
                    { label: 'Ganancia Neta', data: ganancias, backgroundColor: '#198754', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: "'Nunito', sans-serif", size: 13 }, usePointStyle: true } },
                    tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': $' + parseFloat(context.raw).toFixed(2); } } },
                    datalabels: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } } }
                }
            }
        });
    }

    // --- 3. GRÁFICO DE RENDIMIENTO DE VENDEDORES ---
    const canvasVendedores = document.getElementById('graficoVendedores');
    if (canvasVendedores) {
        const ctxVendedores = canvasVendedores.getContext('2d');
        const labelsVendedores = JSON.parse(canvasVendedores.dataset.nombres || '[]');
        const dataVendedores = JSON.parse(canvasVendedores.dataset.ventas || '[]');

        chartVendedoresInstance = new Chart(ctxVendedores, {
            type: 'bar',
            data: {
                labels: labelsVendedores,
                datasets: [{
                    label: 'Recaudado ($)',
                    data: dataVendedores,
                    backgroundColor: paletaColores,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', 
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(context) { return 'Ventas: $' + parseFloat(context.raw).toFixed(2); } } },
                    datalabels: {
                        display: true,
                        color: '#000',
                        align: 'end',
                        anchor: 'end',
                        font: { weight: 'bold' },
                        formatter: (value) => '$' + parseFloat(value).toFixed(2)
                    }
                },
                scales: {
                    x: { beginAtZero: true, grace: '15%' }
                }
            }
        });
    }

    // --- 4. GRÁFICO DE CRECIMIENTO DE CLIENTES ---
    const canvasUsuarios = document.getElementById('graficoUsuarios');
    if (canvasUsuarios) {
        const ctxUsuarios = canvasUsuarios.getContext('2d');
        const etiquetasUsuarios = JSON.parse(canvasUsuarios.dataset.etiquetas || '[]');
        const dataUsuarios = JSON.parse(canvasUsuarios.dataset.usuarios || '[]');

        chartUsuariosInstance = new Chart(ctxUsuarios, {
            type: 'line',
            data: {
                labels: etiquetasUsuarios,
                datasets: [{
                    label: 'Nuevos Clientes',
                    data: dataUsuarios,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.15)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6f42c1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});


// ===============================================================
// EXPORTACIÓN A PDF EN ALTA RESOLUCIÓN (HD)
// ===============================================================
window.procesarGraficosParaPdf = function() {
    
    const canvasBarras = document.getElementById('graficoBarras');
    const canvasDona = document.getElementById('graficoCategorias'); 
    const canvasVendedores = document.getElementById('graficoVendedores'); 
    const canvasUsuarios = document.getElementById('graficoUsuarios'); 
    
    const inputBarras = document.getElementById('grafico_barras_base64');
    const inputDona = document.getElementById('grafico_dona_base64');
    const inputVendedores = document.getElementById('grafico_vendedores_base64');
    const inputUsuarios = document.getElementById('grafico_usuarios_base64');

    const params = new URLSearchParams(window.location.search);
    document.querySelector('input[name="ranking_productos"]').value = params.get('ranking_productos') || 'ventas';
    document.querySelector('input[name="ranking_categorias"]').value = params.get('ranking_categorias') || 'ventas';

    // Función auxiliar para exportar cualquier gráfico en HD (Anti-pixelado)
    const exportarEnHD = (chartInstance, canvas) => {
        if (!chartInstance || !canvas) return '';
        const originalRatio = chartInstance.options.devicePixelRatio || window.devicePixelRatio;
        
        // Subimos la resolución a 2.5x para que sea full HD
        chartInstance.options.devicePixelRatio = 2.5; 
        chartInstance.update('none');
        
        const base64 = canvas.toDataURL('image/png');
        
        // Restauramos a la normalidad
        chartInstance.options.devicePixelRatio = originalRatio; 
        chartInstance.update('none');
        return base64;
    };

    // Exportar gráficos en Alta Resolución
    if (chartBarrasInstance) inputBarras.value = exportarEnHD(chartBarrasInstance, canvasBarras); else inputBarras.value = '';
    if (chartVendedoresInstance) inputVendedores.value = exportarEnHD(chartVendedoresInstance, canvasVendedores); else inputVendedores.value = '';
    if (chartUsuariosInstance) inputUsuarios.value = exportarEnHD(chartUsuariosInstance, canvasUsuarios); else inputUsuarios.value = '';
    
    // Exportar Dona en HD (ahora sin forzar etiquetas)
    if (chartDonaInstance) inputDona.value = exportarEnHD(chartDonaInstance, canvasDona); else inputDona.value = '';

    const formExportar = document.getElementById('formExportarPdf');
    if (formExportar) formExportar.submit();
};

window.aplicarFiltroRapido = function(tipo) {
    const inputInicio = document.getElementById('input_fecha_inicio').value;
    const hoy = new Date();
    let añoBase = hoy.getFullYear(); 

    if (inputInicio && tipo !== 'limpiar') {
        const partes = inputInicio.split('-');
        añoBase = parseInt(partes[0], 10);
    }

    let inicio, fin;
    const formatoFecha = (fecha) => {
        const y = fecha.getFullYear();
        const m = String(fecha.getMonth() + 1).padStart(2, '0');
        const d = String(fecha.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    if (tipo === 'limpiar' || tipo === 'mes_actual') {
        inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        document.getElementById('input_modo_agrupacion').value = '';
    } 
    else if (tipo === 'mes') {
        inicio = new Date(añoBase, 0, 1);
        fin = new Date(añoBase, 11, 31);
        document.getElementById('input_modo_agrupacion').value = tipo;
    } 
    else if (tipo === 'trimestre' || tipo === 'semestre') {
        inicio = new Date(añoBase, 0, 1);
        fin = new Date(añoBase, 11, 31);
        document.getElementById('input_modo_agrupacion').value = tipo;
    } 
    else if (tipo === 'anual') {
        inicio = new Date(añoBase - 1, 0, 1);
        fin = new Date(añoBase + 1, 11, 31);
        document.getElementById('input_modo_agrupacion').value = tipo;
    }

    document.getElementById('input_fecha_inicio').value = formatoFecha(inicio);
    document.getElementById('input_fecha_fin').value = formatoFecha(fin);
    document.getElementById('formFiltroPrincipal').submit();
};

document.addEventListener('livewire:init', () => {
    Livewire.on('actualizarGraficoDona', (event) => {
        if (chartDonaInstance) {
            const data = event[0];
            chartDonaInstance.data.labels = data.labels;
            chartDonaInstance.data.datasets[0].data = data.valores;
            chartDonaInstance.update();
        }
    });
});