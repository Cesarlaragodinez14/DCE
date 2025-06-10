<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit del formulario al cambiar la selección
        var selectElement = document.getElementById('entrega_id');
        if (selectElement) {
            selectElement.addEventListener('change', function() {
                this.form.submit();
            });
        }

        // Inicializar el gráfico si existe el elemento canvas
        var chartElement = document.getElementById('apartadosChart');
        if (chartElement) {
            initializeChart();
        }
    });

    function initializeChart() {
        // Datos para el gráfico definidos como variables globales
        if (typeof chartLabels !== 'undefined' && 
            typeof chartData !== 'undefined' && 
            typeof chartPorcentajes !== 'undefined' &&
            typeof chartTotal !== 'undefined' &&
            typeof chartDistribucion !== 'undefined') {
            
            var ctx = document.getElementById('apartadosChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Claves de Acción Directas',
                            data: chartData,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            order: 3
                        },
                        {
                            label: 'Total (incluye subapartados)',
                            data: chartTotal,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            order: 2
                        },
                        {
                            label: 'Distribución por Apartado (%)',
                            data: chartDistribucion,
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            order: 1,
                            type: 'line',
                            fill: false,
                            tension: 0.4,
                            pointStyle: 'circle',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Claves'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Distribución (%)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    const index = context.dataIndex;
                                    
                                    if (context.datasetIndex === 0) { // Claves directas
                                        return `${datasetLabel}: ${value} (${chartPorcentajes[index]}%)`;
                                    } else if (context.datasetIndex === 1) { // Total
                                        return `${datasetLabel}: ${value}`;
                                    } else { // Distribución
                                        return `${datasetLabel}: ${value}%`;
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script> 