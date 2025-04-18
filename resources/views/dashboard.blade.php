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
                    <a href="#" class="small-box-footer">Mas información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>53<sup style="font-size: 20px">%</sup></h3>
                        <p>porcentaje de compra</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mas información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>71</h3>
                        <p>Ventas del mes</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mas información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>111</h3>
                        <p>Productos registrados</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mas información <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

        </div>


        <div class="row">

            <section class="col-lg-7 connectedSortable">


                <x-adminlte-card title="Grafico de ventas diarias" icon="fas fa-lg fa-fan" removable collapsible>
                    <canvas id="ventas"></canvas>
                </x-adminlte-card>


            </section>
            <section class="col-lg-5 connectedSortable">
                <div class="card bg-gradient">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-th mr-1"></i>
                            Gráfico de ventas totales
                        </h3>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-center align-items-center" style="height: 450px;"> <!-- Ajusta la altura según sea necesario -->
                            <canvas id="Donut" width="485" height="485"></canvas>
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

