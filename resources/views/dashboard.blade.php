@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
<h2>Panel administrativo</h2>
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


        <div class="row">

        <section class="col-lg-7 connectedSortable">
        <x-adminlte-card title="Ventas por Lugar" icon="fas fa-lg fa-chart-line" removable collapsible>
            <canvas id="ventasPorLugar"></canvas>
        </x-adminlte-card>
    </section>
    
    <section class="col-lg-5 connectedSortable">
        <div class="card bg-gradient">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-th mr-1"></i>
                    Ventas por Técnico
                </h3>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-center align-items-center" style="height: 450px;">
                    <canvas id="ventasPorTecnico"></canvas>
                </div>
            </div>
        </div>
    </section>

        </div>

    </div>
</section>
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
</style>




@stop

@section('css')
{{-- --}}
<link rel="stylesheet" href="{{ asset('/build/assets/admin/admin.css') }}">

@stop

@section('js')
<canvas id="ventasPorLugar"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
 const datosLugar = <?php echo json_encode($ventasPorLugar); ?>;

// Mapear número de mes a nombre
const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

// Extraer meses únicos ordenados
const meses = [...new Set(datosLugar.map(item => item.mes))].sort((a,b) => a - b);

// Extraer lugares únicos
const lugares = [...new Set(datosLugar.map(item => item.lugarventa))];

// Preparar datasets
const datasetsLugar = lugares.map(lugar => {
    const datos = meses.map(mes => {
        const registro = datosLugar.find(item => item.mes === mes && item.lugarventa === lugar);
        return registro ? registro.total : 0;
    });

    // Color aleatorio para cada línea
    const color = `rgb(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)})`;

    return {
        label: lugar,
        data: datos,
        borderColor: color,
        backgroundColor: color.replace(')', ', 0.2)').replace('rgb', 'rgba'),
        tension: 0.1,
        fill: true
    };
});

    
    // Crear el gráfico de líneas
    const ctxLugar = document.getElementById('ventasPorLugar').getContext('2d');

const lineChart = new Chart(ctxLugar, {
    type: 'line',
    data: {
        labels: meses.map(m => nombresMeses[m - 1]), // Convertir números a nombres
        datasets: datasetsLugar  
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Ventas por Lugar de Venta por Mes'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        }
    }
});


    // Gráfico de dona para ventas por técnico
    const ctxTecnico = document.querySelector('#ventasPorTecnico');
    const datosTecnico = <?php echo json_encode($ventasPorTecnico); ?>;
    
    const donutChart = new Chart(ctxTecnico, {
        type: 'doughnut',
        data: {
            labels: datosTecnico.map(item => item.tecnico),
            datasets: [{
                label: 'Monto Total',
                data: datosTecnico.map(item => item.monto_total),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Ventas por Técnico'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@stop