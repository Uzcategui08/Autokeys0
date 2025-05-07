@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
<h2 >Bienvenido!</h2>
<hr>
@stop

@section('content')

<head>
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
</head>



<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-3 col-6">

                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $productos}}</h3>
                        <p>Productos en almacen</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$diferencia_porcentual}}<sup style="font-size: 20px">%</sup></h3>
                        <p>Evolucion de Facturación</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$registros}}</h3>
                        <p>Ventas del mes</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>

                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{$valorV}}</h3>
                        <p>Facturación Mensual</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>

                </div>
            </div>

        </div>
        @unless(auth()->user()->hasRole('limited_user'))
        <div class="row">
    <!-- Card de Ventas por Lugar - Versión Compacta -->
    <section class="col-lg-7 connectedSortable mb-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-header bg-gradient-primary text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line mr-2"></i>
                        Ventas por Lugar
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" style="height: 300px;">
                <canvas id="ventasPorLugar" style="width: 100%; height: 100%; padding: 15px;"></canvas>
            </div>
        </div>
    </section>

    <!-- Card de Ventas por Técnico - Versión Compacta -->
    <section class="col-lg-5 connectedSortable mb-4">
        <div class="card shadow-lg border-0 h-100">
            <div class="card-header bg-gradient-info text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i>
                        Ventas por Técnico
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" style="height: 300px;">
                <canvas id="ventasPorTecnico" style="width: 100%; height: 100%; padding: 15px;"></canvas>
            </div>
        </div>
    </section>
</div>
@endunless
<style>
    .card-footer {
        display: flex;
        justify-content: center;
        /* Centra horizontalmente */
        align-items: center;
        /* Centra verticalmente */
        height: 485px;
        /* Ajusta esta altura según tus necesidades */
    }

    canvas {
        max-width: 100%;
        /* Asegura que el canvas no exceda el ancho del contenedor */
        height: 485;
        /* Mantiene la proporción del canvas */
    }

    .chart-container {
    background-color: #f8f9fa;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 20px;
    transition: all 0.3s ease;
}

.chart-container:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}
/* Para un ajuste más fino en móviles */
@media (max-width: 768px) {
    .card-body {
        height: 250px !important;
    }
    
    .card-header h3 {
        font-size: 1rem;
    }
}
</style>




@stop

@section('css')
{{-- --}}
<link rel="stylesheet" href="{{ asset('/build/assets/admin/admin.css') }}">

@stop

@section('js')

<script>
    // Gráfico de dona mejorado para ventas por técnico
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTecnico = document.getElementById('ventasPorTecnico').getContext('2d');
        const datosTecnico = @json($ventasPorTecnico);
        
        // Colores sólidos vibrantes
        const coloresSolidos = [
            '#FF6384', // Rojo
            '#36A2EB', // Azul
            '#FFCE56', // Amarillo
            '#4BC0C0', // Turquesa
            '#9966FF', // Morado
            '#FF9F40', // Naranja
            '#8AC24A', // Verde
            '#EA5F89', // Rosa oscuro
            '#00BFFF', // Azul cielo
            '#FFD700'  // Oro
        ];
    
        const donutChart = new Chart(ctxTecnico, {
            type: 'doughnut',
            data: {
                labels: datosTecnico.map(item => item.tecnico),
                datasets: [{
                    label: 'Monto Total',
                    data: datosTecnico.map(item => item.monto_total),
                    backgroundColor: coloresSolidos,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 20,
                    cutout: '65%' // Controla el tamaño del agujero central
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 20,
                        right: 20
                    }
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 14,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                                weight: 'bold'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Ventas por Técnico',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toLocaleString('es-MX')} (${percentage}%)`;
                            }
                        },
                        bodyFont: {
                            size: 14
                        },
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    datalabels: {
                        display: false
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                elements: {
                    arc: {
                        borderWidth: 3
                    }
                }
            }
        });
    
        // Hacer el gráfico responsive
        window.addEventListener('resize', function() {
            donutChart.resize();
        });
    });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const datosLugar = <?php echo json_encode($ventasPorLugar); ?>;
    
    // Paleta de colores profesional
    const colores = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', 
        '#e74a3b', '#858796', '#5a5c69', '#00a3e1',
        '#ff6384', '#fd7e14'
    ];

    // Mapear número de mes a nombre
    const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Extraer meses únicos ordenados
    const meses = [...new Set(datosLugar.map(item => item.mes))].sort((a,b) => a - b);

    // Extraer lugares únicos
    const lugares = [...new Set(datosLugar.map(item => item.lugarventa))];

    // Preparar datasets
    const datasetsLugar = lugares.map((lugar, index) => {
        const datos = meses.map(mes => {
            const registro = datosLugar.find(item => item.mes === mes && item.lugarventa === lugar);
            return registro ? registro.total : 0;
        });

        return {
            label: lugar,
            data: datos,
            borderColor: colores[index % colores.length],
            backgroundColor: colores[index % colores.length] + '33', // Agrega transparencia
            borderWidth: 2,
            tension: 0.3,
            fill: false,
            pointBackgroundColor: colores[index % colores.length],
            pointRadius: 4,
            pointHoverRadius: 6
        };
    });

    // Crear el gráfico de líneas
    const ctxLugar = document.getElementById('ventasPorLugar').getContext('2d');
    
    const lineChart = new Chart(ctxLugar, {
        type: 'line',
        data: {
            labels: meses.map(m => nombresMeses[m - 1]),
            datasets: datasetsLugar  
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 12,
                            family: "'Segoe UI', Tahoma, sans-serif"
                        },
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                title: {
                    display: true,
                    text: 'Evolución de Ventas por Lugar',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: $${context.raw.toLocaleString('es-MX')}`;
                        },
                        labelColor: function(context) {
                            return {
                                borderColor: 'transparent',
                                backgroundColor: context.dataset.borderColor,
                                borderRadius: 2
                            };
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-MX');
                        },
                        font: {
                            size: 11
                        }
                    },
                    title: {
                        display: true,
                        text: 'Monto en Pesos',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    borderWidth: 2
                }
            }
        }
    });

    // Hacer el gráfico responsive al redimensionar
    window.addEventListener('resize', function() {
        lineChart.resize();
    });
});
</script>
@stop