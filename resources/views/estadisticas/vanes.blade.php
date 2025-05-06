@php
use Carbon\Carbon;
@endphp

@extends('adminlte::page')

@section('title', 'Estadísticas Vans')

@section('content_header')
    <h1>Estadísticas por Van</h1>
@stop

@section('content')
    <!-- Resumen de Totales -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Van Grande-Pulga</span>
                    <span class="info-box-number">Ventas: ${{ number_format($totales['ventasGrande'], 2) }}</span>
                    <span class="info-box-number">Gastos: ${{ number_format($totales['gastosGrande'], 2) }}</span>
                    <span class="info-box-number">Costos: ${{ number_format($totales['costosGrande'], 2) }}</span>
                    <span class="info-box-number">Cerrajero %: ${{ number_format($totales['porcentajeCerrajeroGrande'], 2) }}</span>
                    <span class="info-box-number">Total: ${{ number_format($totales['ventasGrande'] - $totales['gastosGrande'] - $totales['costosGrande'] - $totales['porcentajeCerrajeroGrande'], 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Van Pequeña-Pulga</span>
                    <span class="info-box-number">Ventas: ${{ number_format($totales['ventasPequena'], 2) }}</span>
                    <span class="info-box-number">Gastos: ${{ number_format($totales['gastosPequena'], 2) }}</span>
                    <span class="info-box-number">Costos: ${{ number_format($totales['costosPequena'], 2) }}</span>
                    <span class="info-box-number">Cerrajero %: ${{ number_format($totales['porcentajeCerrajeroPequena'], 2) }}</span>
                    <span class="info-box-number">Total: ${{ number_format($totales['ventasPequena'] - $totales['gastosPequena'] - $totales['costosPequena'] - $totales['porcentajeCerrajeroPequena'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gastos por Van -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Gastos - {{ $vanGrande }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="gastosGrandeTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastosVanGrande as $gasto)
                            <tr>
                                <td>{{ Carbon::parse($gasto->f_gastos)->format('d/m/Y') }}</td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>${{ number_format($gasto->valor, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $gasto->estatus == 'pagado' ? 'success' : 'danger' }}">
                                        {{ ucfirst($gasto->estatus) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay gastos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Gastos - {{ $vanPequena }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="gastosPequenaTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastosVanPequena as $gasto)
                            <tr>
                                <td>{{ Carbon::parse($gasto->f_gastos)->format('d/m/Y') }}</td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>${{ number_format($gasto->valor, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $gasto->estatus == 'pagado' ? 'success' : 'danger' }}">
                                        {{ ucfirst($gasto->estatus) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay gastos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Costos por Van -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Costos - {{ $vanGrande }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="costosGrandeTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costosVanGrande as $costo)
                            <tr>
                                <td>{{ Carbon::parse($costo->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $costo->descripcion }}</td>
                                <td>${{ number_format($costo->valor, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay costos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Costos - {{ $vanPequena }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="costosPequenaTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costosVanPequena as $costo)
                            <tr>
                                <td>{{ Carbon::parse($costo->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $costo->descripcion }}</td>
                                <td>${{ number_format($costo->valor, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay costos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas por Van (opcional) -->
    {{-- Puedes agregar aquí la tabla de ventas si la necesitas --}}
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <!-- jQuery y DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#gastosGrandeTable, #gastosPequenaTable, #costosGrandeTable, #costosPequenaTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "order": [[0, "desc"]],
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "info": false,
                "autoWidth": false
            });
        });
    </script>
@stop
