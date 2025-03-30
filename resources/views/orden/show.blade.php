@extends('adminlte::page')

@section('title', 'Ordenes')

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
                            <span class="card-title">{{ __('Orden') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('ordens.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <strong>{{ __('ID Orden') }}:</strong>
                                    <p class="form-control-static">{{ $orden->id_orden }}</p>
                                </div>
                                <div class="form-group mb-2">
                                    <strong>{{ __('Cliente') }}:</strong>
                                    <p class="form-control-static">
                                        {{ $orden->cliente->nombre ?? 'N/A' }} <!-- Asume que hay una relación "cliente" -->
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Fecha de la orden') }}:</strong>
                                    <p class="form-control-static">
                                        {{ \Carbon\Carbon::parse($orden->f_orden)->format('d/m/Y') }} 
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Dirección') }}:</strong>
                                    <p class="form-control-static">{{ $orden->direccion }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Técnico') }}:</strong>
                                    <p class="form-control-static">{{ $orden->id_tecnico }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Estado') }}:</strong>
                                    <p class="form-control-static">
                                        <span class="badge 
                                            @if($orden->estado == 'aprobado') badge-success 
                                            @elseif($orden->estado == 'pendiente') badge-warning 
                                            @elseif($orden->estado == 'rechazado') badge-danger 
                                            @else badge-secondary 
                                            @endif">
                                            {{ ucfirst($orden->estado) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Ítems del orden') }}:</strong>
                                    @if (is_array($orden->items) && count($orden->items) > 0)
                                        <div class="table-responsive">
                                            @foreach ($orden->items as $groupIndex => $itemGroup)
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
