// Declaramos las variables globales para almacenar las instancias de Chart.js
let chartDonaInstance = null;
let chartBarrasInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    
    // 0. Registramos el plugin de etiquetas
    if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels);
    }

    // --- 1. GRÁFICO DE DONA ---
    const canvasDona = document.getElementById('graficoCategorias');
    if (canvasDona) {
        const ctxDona = canvasDona.getContext('2d');
        const labels = JSON.parse(canvasDona.dataset.nombres || '[]');
        const data = JSON.parse(canvasDona.dataset.cantidades || '[]');
        const colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#0dcaf0'];

        chartDonaInstance = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colores,
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
                        labels: { 
                            padding: 15, 
                            font: { family: "'Nunito', sans-serif", size: 12 },
                            usePointStyle: true 
                        }
                    },
                    // ==========================================
                    // CONFIGURACIÓN DE ETIQUETAS MEJORADA
                    // ==========================================
                    datalabels: {
                        display: false, // 🔴 OCULTAS POR DEFECTO EN LA WEB
                        color: '#ffffff',
                        backgroundColor: 'rgba(0, 0, 0, 0.65)', // Fondo limpio y nítido (cero píxeles borrosos)
                        borderRadius: 4,
                        padding: 4,
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        textAlign: 'center',
                        formatter: (value, context) => {
                            if (value === 0) return null;
                            const nombreCategoria = context.chart.data.labels[context.dataIndex];
                            return nombreCategoria + '\n' + value + ' unid.';
                        }
                    }
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
                    {
                        label: 'Ingresos (Ventas Brutas)',
                        data: ventas,
                        backgroundColor: '#0d6efd',
                        borderRadius: 4
                    },
                    {
                        label: 'Costos (Gastos de Inventario)',
                        data: gastos,
                        backgroundColor: '#dc3545',
                        borderRadius: 4
                    },
                    {
                        label: 'Ganancia Neta',
                        data: ganancias,
                        backgroundColor: '#198754',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: "'Nunito', sans-serif", size: 13 }, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw.toFixed(2);
                            }
                        }
                    },
                    datalabels: {
                        display: false, // Siempre apagadas en las barras ya que se amontonan cuando el rango es por dias
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
});

// ==========================================
// FUNCIÓN MÁGICA DE EXPORTACIÓN (ACTUALIZADA)
// ==========================================
window.exportarConGraficos = function() {
    
    const canvasBarras = document.getElementById('graficoBarras');
    const canvasDona = document.getElementById('graficoCategorias'); 
    
    const inputBarras = document.getElementById('grafico_barras_base64');
    const inputDona = document.getElementById('grafico_dona_base64');

    // 1. Gráfico de Barras a imagen
    if (canvasBarras) {
        inputBarras.value = canvasBarras.toDataURL('image/png');
    } else {
        inputBarras.value = '';
    }

    // 2. Gráfico de Dona a imagen (CON RE-RENDERIZADO INMEDIATO)
    if (canvasDona && chartDonaInstance) {
        
        // A) Encendemos las etiquetas temporalmente
        chartDonaInstance.options.plugins.datalabels.display = true;
        
        // B) Forzamos un repintado INMEDIATO (sin animaciones) usando 'none'
        chartDonaInstance.update('none'); 
        
        // C) Ahora sí, tomamos la foto perfecta con los datos ya dibujados
        inputDona.value = canvasDona.toDataURL('image/png');
        
        // D) Las apagamos de nuevo para que la web siga limpia
        chartDonaInstance.options.plugins.datalabels.display = false;
        
        // E) Volvemos al estado normal inmediatamente
        chartDonaInstance.update('none');

    } else {
        inputDona.value = '';
    }

    // 3. Enviamos el formulario
    const formExportar = document.getElementById('formExportarPdf');
    if (formExportar) {
        formExportar.submit();
    } else {
        console.error("No se encontró el formulario formExportarPdf");
    }
};

// ==========================================
// FILTROS DE ACCIÓN RÁPIDA AVANZADOS
// ==========================================
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
        // Volvemos a la fecha por defecto: Mes actual en curso
        inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        document.getElementById('input_modo_agrupacion').value = ''; // Borramos el modo de agrupación
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

// 1. Escuchar a Livewire para animar el Gráfico de Dona sin recargar la pantalla
document.addEventListener('livewire:init', () => {
    Livewire.on('actualizarGraficoDona', (event) => {
        if (chartDonaInstance) {
            const data = event[0]; // Extraemos los datos que envió PHP
            chartDonaInstance.data.labels = data.labels;
            chartDonaInstance.data.datasets[0].data = data.valores;
            chartDonaInstance.update();
        }
    });
});

// 2. Ajuste en la función exportarConGraficos para enviar la URL actual
window.exportarConGraficos = function() {
    const canvasBarras = document.getElementById('graficoBarras');
    const canvasDona = document.getElementById('graficoCategorias'); 
    const inputBarras = document.getElementById('grafico_barras_base64');
    const inputDona = document.getElementById('grafico_dona_base64');

    // Capturamos los filtros de Livewire directamente de la URL
    const params = new URLSearchParams(window.location.search);
    document.querySelector('input[name="ranking_productos"]').value = params.get('ranking_productos') || 'ventas';
    document.querySelector('input[name="ranking_categorias"]').value = params.get('ranking_categorias') || 'ventas';

    // (Aquí va tu código original intacto de canvasBarras y canvasDona.toDataURL...)
    // ...
    // ...

    const formExportar = document.getElementById('formExportarPdf');
    if (formExportar) {
        formExportar.submit();
    }
};