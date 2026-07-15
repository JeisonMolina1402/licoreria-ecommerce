// Declaramos las variables globales para almacenar las instancias de Chart.js
let chartDonaInstance = null;
let chartBarrasInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. GRÁFICO DE DONA ---
    const canvasDona = document.getElementById('graficoCategorias');
    if (canvasDona) {
        const ctxDona = canvasDona.getContext('2d');
        const labels = JSON.parse(canvasDona.dataset.nombres || '[]');
        const data = JSON.parse(canvasDona.dataset.cantidades || '[]');
        const colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#0dcaf0'];

        // Guardamos la instancia en nuestra variable global
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

        // Guardamos la instancia en nuestra variable global
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

// FUNCIÓN MÁGICA: Convierte los gráficos a imágenes y envía el formulario
window.exportarConGraficos = function() {
    const inputBarras = document.getElementById('inputGraficoBarras');
    const inputDona = document.getElementById('inputGraficoDona');
    const form = document.getElementById('formExportarPdf');

    // Convertimos el gráfico de barras a Base64 si existe en pantalla
    if (chartBarrasInstance) {
        inputBarras.value = chartBarrasInstance.toBase64Image();
    }

    // Convertimos el gráfico de dona a Base64 si existe en pantalla
    if (chartDonaInstance) {
        inputDona.value = chartDonaInstance.toBase64Image();
    }

    // Enviamos el formulario al controlador
    form.submit();
};