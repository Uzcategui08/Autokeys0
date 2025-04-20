@extends('adminlte::page')

@section('title', 'Registro V')

@section('content_header')
<h1>Detalle del Registro V</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-productos: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Registro V') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('registro-vs.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Fecha') }}:</strong>
                                    <p class="form-control-static">
                                        {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Técnico') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->tecnico }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Trabajo') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->trabajo }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Cliente') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->cliente }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Teléfono') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->telefono }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Valor V') }}:</strong>
                                    <p class="form-control-static">{{ number_format($registroV->valor_v, 2) }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('estatus') }}:</strong>
                                    <p class="form-control-static">
                                        <span class="badge 
                                            @if($registroV->estatus == 'pagado') badge-success 
                                            @elseif($registroV->estatus == 'parcialmentep') badge-warning 
                                            @elseif($registroV->estatus == 'pendiente') badge-danger 
                                            @else badge-secondary 
                                            @endif">
                                            {{ ucfirst($registroV->estatus) }} <!-- Mostrar estatus con estilo -->
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Método de Pago') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->metodo_p }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Titular de Cuenta') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->titular_c }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Cobro') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->cobro }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Descripción CE') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->descripcion_ce }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Monto CE') }}:</strong>
                                    <p class="form-control-static">{{ number_format($registroV->monto_ce, 2) }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Método PCE') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->metodo_pce }}</p>
                                </div>
                                <div class="form-group mb-4">
                                    <strong>{{ __('Porcentaje C') }}:</strong>
                                    <p class="form-control-static">{{ $registroV->porcentaje_c }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <strong>{{ __('Vehículo') }}:</strong>
                                    <p class="form-control-static">
                                        {{ $registroV->marca }} {{ $registroV->modelo }} ({{ $registroV->año }})
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                <strong>{{ __('Ítems del registroV') }}:</strong>
                                    @if (is_array($registroV->items) && count($registroV->items) > 0)
                                        <div class="table-responsive">
                                            @foreach ($registroV->items as $groupIndex => $itemGroup)
                                                <h5 class="mt-4">{{ __('Trabajo') }}: {{ $itemGroup['trabajo'] ?? 'N/A' }}</h5>
                                                {{-- <h6>{{ __('Almacén') }}: {{ $itemGroup['almacen'] ?? 'N/A' }}</h6> --}}
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
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
                                                                <td>{{$producto['id_producto'] ?? 'N/A' }}</td>
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