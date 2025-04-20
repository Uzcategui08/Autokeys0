@extends('adminlte::page')

@section('title', 'Cuotas del Préstamo #' . $prestamo->id_prestamos)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Cuotas del Préstamo #{{ $prestamo->id_prestamos }}</h1>
        <a href="{{ route('prestamos.empleado', $prestamo->id_empleado) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Volver a préstamos de {{ $prestamo->empleado->nombre }}
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información del Préstamo</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Empleado:</dt>
                                    <dd class="col-sm-8">{{ $prestamo->empleado->nombre }}</dd>
                                    
                                    <dt class="col-sm-4">Valor Total:</dt>
                                    <dd class="col-sm-8">${{ number_format($prestamo->valor, 2) }}</dd>
                                    
                                    <dt class="col-sm-4">Cuotas:</dt>
                                    <dd class="col-sm-8">{{ $prestamo->cuota_actual }} de {{ $prestamo->cuotas }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Estado:</dt>
                                    <dd class="col-sm-8">
                                        @if($prestamo->activo)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-secondary">Completado</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-4">Cuotas pagadas:</dt>
                                    <dd class="col-sm-8">
                                        {{ $prestamo->cuotas()->where('pagada', true)->count() }}
                                    </dd>
                                    
                                    <dt class="col-sm-4">Total pagado:</dt>
                                    <dd class="col-sm-8">
                                        ${{ number_format($prestamo->cuotas()->where('pagada', true)->sum('valor'), 2) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detalle de Cuotas</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @if($lista_cuotas->count() > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                        <th>Fecha Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lista_cuotas as $cuota)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>${{ number_format($cuota->valor, 2) }}</td>
                                            <td>
                                                @if($cuota->pagada)
                                                    <span class="badge badge-success">Pagada</span>
                                                @else
                                                    <span class="badge badge-warning">Pendiente</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cuota->pagada)
                                                    {{ $cuota->updated_at->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info">No se encontraron cuotas para este préstamo</div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .badge {
            padding: 5px 10px;
            font-size: 12px;
            font-weight: normal;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .card-info {
            border-top: 3px solid #17a2b8;
        }
    </style>
@endpush