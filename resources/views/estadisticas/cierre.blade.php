@extends('adminlte::page')

@section('title', 'Estadísticas Financieras')

@section('content_header')
<div class="container">
    <h2>Estadísticas Financieras</h2>
    
    <!-- Card de Conteo de Trabajos -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Conteo de Trabajos</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Trabajo</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conteoTrabajos as $trabajo)
                            <tr>
                                <td>{{ $trabajo->trabajo }}</td>
                                <td>{{ $trabajo->cantidad }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th>Total General</th>
                                <th>{{ $totalGeneral }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Card de Ventas por Almacén -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Ventas por Almacén</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Almacén</th>
                                <th>Total Ventas</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasPorAlmacen as $almacen)
                            <tr>
                                <td>{{ $almacen->lugarventa }}</td>
                                <td>${{ number_format($almacen->total_ventas, 2) }}</td>
                                <td>{{ $almacen->cantidad_ventas }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th>Total General</th>
                                <th>${{ number_format($totalVentas, 2) }}</th>
                                <th>{{ $ventasPorAlmacen->sum('cantidad_ventas') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection