@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Detalle</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Presupuesto') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('presupuestos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="row">
                            <!-- Información del Presupuesto -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <strong>{{ __('ID Presupuesto') }}:</strong>
                                    <p class="form-control-static">{{ $presupuesto->id_presupuesto }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Cliente') }}:</strong>
                                    <p class="form-control-static">
                                        {{ $presupuesto->cliente->nombre ?? 'N/A' }} <!-- Asume que hay una relación "cliente" -->
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Fecha del Presupuesto') }}:</strong>
                                    <p class="form-control-static">
                                        {{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('d/m/Y') }} <!-- Formato de fecha -->
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Validez') }}:</strong>
                                    <p class="form-control-static">
                                        {{ \Carbon\Carbon::parse($presupuesto->validez)->format('d/m/Y') }} <!-- Formato de fecha -->
                                    </p>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Descuento') }}:</strong>
                                    <p class="form-control-static">
                                        {{ number_format($presupuesto->descuento, 2) }}% <!-- Formato de porcentaje -->
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('IVA') }}:</strong>
                                    <p class="form-control-static">
                                        {{ number_format($presupuesto->iva, 2) }}% <!-- Formato de porcentaje -->
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Estado') }}:</strong>
                                    <p class="form-control-static">
                                        <span class="badge 
                                            @if($presupuesto->estado == 'aprobado') badge-success 
                                            @elseif($presupuesto->estado == 'pendiente') badge-warning 
                                            @elseif($presupuesto->estado == 'rechazado') badge-danger 
                                            @else badge-secondary 
                                            @endif">
                                            {{ ucfirst($presupuesto->estado) }} <!-- Mostrar estado con estilo -->
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Mostrar ítems del presupuesto -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Ítems del Presupuesto') }}:</strong>
                                    @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio Unitario</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total = 0;
                                                    @endphp
                                                    @foreach ($presupuesto->items as $item)
                                                        @php
                                                            $subtotal = $item['cantidad'] * $item['precio_producto'];
                                                            $total += $subtotal;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item['nombre_producto'] ?? 'N/A' }}</td> <!-- Mostrar el nombre del producto -->
                                                            <td>{{ $item['cantidad'] ?? 'N/A' }}</td>
                                                            <td>{{ number_format($item['precio_producto'], 2) }}</td>
                                                            <td>{{ number_format($subtotal, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                        <td>{{ number_format($total, 2) }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No hay ítems registrados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection