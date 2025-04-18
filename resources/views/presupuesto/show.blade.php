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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Ítems del presupuesto') }}:</strong>
                                    @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
                                        <div class="table-responsive">
                                            @foreach ($presupuesto->items as $groupIndex => $itemGroup)
                                                <h5 class="mt-4">{{ __('Trabajo') }}: {{ $itemGroup['trabajo'] ?? 'N/A' }}</h5>
                                                {{-- <h6>{{ __('Almacén') }}: {{ $itemGroup['almacen'] ?? 'N/A' }}</h6> --}}
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
                                                            $groupTotal = 0;
                                                        @endphp
                                                        @foreach ($itemGroup['productos'] as $producto)
                                                            @php
                                                                $subtotal = $producto['cantidad'] * $producto['precio_producto'];
                                                                $groupTotal += $subtotal;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                                                                <td>{{ $producto['cantidad'] ?? 'N/A' }}</td>
                                                                <td>{{ number_format($producto['precio_producto'], 2) }}</td>
                                                                <td>{{ number_format($subtotal, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-right"><strong>Total del grupo:</strong></td>
                                                            <td>{{ number_format($groupTotal, 2) }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            @endforeach
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