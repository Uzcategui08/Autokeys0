@extends('adminlte::page')

@section('title', 'Estadísticas Financieras')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
        <h1>Reporte Financiero - {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}</h1>
        <button onclick="window.print()" class="btn btn-success mr-2">
            <i class="fas fa-print"></i> Imprimir Reporte
        </button>
        
        <form method="GET" action="{{ route('estadisticas.ventas') }}" class="form-inline">
            <div class="form-group mr-2">
                <select name="month" class="form-control">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == $monthSelected ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mr-2">
                <select name="year" class="form-control">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            
            @if(request()->has('month') || request()->has('year'))
                <a href="{{ route('estadisticas.ventas') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
        </form>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Resumen Ejecutivo -->
    @if($noData ?? false)
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h3>No hay datos de ventas para {{ DateTime::createFromFormat('!m', $monthSelected)->format('F') }} de {{ $yearSelected }}</h3>
        <p class="mt-3">Por favor, seleccione otro período.</p>
    </div>
@else
    <!-- Tu código actual de visualización de estadísticas -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Facturación del Mes</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-center">${{ number_format($stats['ventas']['facturacion'], 2) }}</h3>
                </div>
            </div>
        </div>
    <div class="card mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h3 class="card-title">Resumen Ejecutivo</h3>
            <div class="card-tools">
                <span class="badge bg-light text-dark">
                    {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">Facturación</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['ventas']['facturacion'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    Cobrado: ${{ number_format($stats['ventas']['cobrado_mes'], 2) }}<br>
                                    Transacciones: {{ $stats['ventas']['num_transacciones'] }}<br>
                                    Ticket promedio: ${{ number_format($stats['ventas']['ticket_promedio'], 2) }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">Utilidad Bruta</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}% de facturación<br>
                                    Costo venta: ${{ number_format($stats['costos']['total_costo_venta'], 2) }}<br>
                                    ({{ number_format($stats['costos']['porcentaje_costo_venta'], 2) }}%)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">Utilidad Neta</div>
                        <div class="card-body">
                            <h4 class="card-title">${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</h4>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}% de facturación<br>
                                    Total gastos: ${{ number_format($stats['gastos']['total_gastos'], 2) }}<br>
                                    ({{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title">Detalle de Ventas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Concepto</th>
                            <th>Valor</th>
                            <th>% Facturación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Facturación Total</td>
                            <td>${{ number_format($stats['ventas']['facturacion'], 2) }}</td>
                            <td>100.00%</td>
                        </tr>
                        <tr>
                            <td>Cobrado del Mes</td>
                            <td>${{ number_format($stats['ventas']['cobrado_mes'], 2) }}</td>
                            <td>{{ number_format($stats['ventas']['cobrado_mes'] / $stats['ventas']['facturacion'] * 100, 2) }}%</td>
                        </tr>
                        <tr>
                            <td>Evolución Facturación (vs mes anterior)</td>
                            <td colspan="2">{{ number_format($stats['ventas']['evolucion_facturacion'], 2) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalle de Costos y Gastos -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h3 class="card-title">Detalle de Costos y Gastos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Concepto</th>
                            <th>Valor</th>
                            <th>% Facturación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Costo de Venta</strong></td>
                            <td>${{ number_format($stats['costos']['total_costo_venta'], 2) }}</td>
                            <td>{{ number_format($stats['costos']['porcentaje_costo_venta'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Utilidad Bruta</strong></td>
                            <td>${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</td>
                            <td>{{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;Gasto Personal</td>
                            <td>${{ number_format($stats['gastos']['personal']['total'], 2) }}</td>
                            <td>{{ number_format($stats['gastos']['personal']['porcentaje'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;Gastos Operativos</td>
                            <td>${{ number_format($stats['gastos']['operativos']['total'], 2) }}</td>
                            <td>{{ number_format($stats['gastos']['operativos']['porcentaje'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;Otros Gastos</td>
                            <td>${{ number_format($stats['gastos']['otros']['total'], 2) }}</td>
                            <td>{{ number_format($stats['gastos']['otros']['porcentaje'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;Financieros e Impuestos</td>
                            <td>${{ number_format($stats['gastos']['financieros_impuestos']['total'], 2) }}</td>
                            <td>{{ number_format($stats['gastos']['financieros_impuestos']['porcentaje'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Total Gastos</strong></td>
                            <td>${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
                            <td>{{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Utilidad Neta</strong></td>
                            <td>${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</td>
                            <td>{{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endif
@stop

@section('css')
<style>
    .card-header {
        font-weight: bold;
    }
    .table th {
        background-color: #343a40;
        color: white;
    }
    .table tbody tr:hover {
        background-color: rgba(0,0,0,.05);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .container-fluid, .container-fluid * {
            visibility: visible;
        }
        .container-fluid {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header, .table th {
            background-color: #343a40 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    }
</style>
@stop