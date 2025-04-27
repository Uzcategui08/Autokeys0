@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Registro de Ventas V #{{ $registroV->id }}</h1>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            Venta: <i class="fas "> {{ $registroV->lugarventa }}</i>
                            <small class="float-right">Fecha: {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('m/d/Y') }}</small>
                        </h4>
                    </div>
                </div>

                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <strong>Técnico</strong>
                        <address>
                            {{ $registroV->tecnico }}<br>
                            {{ $registroV->trabajo }}<br>
                            Estatus: <span class="badge 
                                @if($registroV->estatus == 'pagado') badge-success 
                                @elseif($registroV->estatus == 'parcialementep') badge-warning 
                                @elseif($registroV->estatus == 'pendiente') badge-danger 
                                @else badge-secondary 
                                @endif">
                                {{ ucfirst($registroV->estatus) }}
                            </span>
                        </address>
                    </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Modelo') }}</label>
                                <p class="form-control-static">{{ $registroV->modelo }}</p>
                            </div>

                    <div class="col-sm-4 invoice-col">
                        <b>Registro V #{{ $registroV->id }}</b><br>
                        
                        <b>Valor Total:</b> ${{ number_format($registroV->valor_v, 2) }}<br>
                        <b>Método Pago:</b> {{ $registroV->metodo_p }}<br>
                        <b>Titular:</b> {{ $registroV->titular_c }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach($registroV->items as $itemGroup)
                                    @foreach($itemGroup['productos'] as $producto)
                                        @php
                                            $subtotal = $producto['cantidad'] * $producto['precio_producto'];
                                            $total += $subtotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $producto['cantidad'] }}</td>
                                            <td>{{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                                            <td>{{ $producto['id_producto'] ?? 'N/A' }}</td>
                                            <td>${{ number_format($producto['precio_producto'], 2) }}</td>
                                            <td>${{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                <div class="alert alert-warning">No hay items de trabajo registrados</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

                <div class="row">
                    <div class="col-6">
                        <p class="lead">Costos Extras:</p>
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Descripción</th>
                                    <td>{{ $registroV->descripcion_ce ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Monto</th>
                                    <td>${{ number_format($registroV->monto_ce, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Método Pago</th>
                                    <td>{{ $registroV->metodo_pce ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>% Cerrajero</th>
                                    <td>{{ $registroV->porcentaje_c }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-6">
                        <p class="lead">Resumen Financiero</p>
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Costos Extras:</th>
                                    <td>${{ number_format($registroV->monto_ce, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>${{ number_format($registroV->valor_v, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row no-print">
                    <div class="col-12">
                        <a href="javascript:window.print()" class="btn btn-default"><i class="fas fa-print"></i> Imprimir</a>
                        <a href="{{ route('registro-vs.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Botones de acción -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <a href="{{ route('registro-vs.edit', $registroV->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Editar
                    </a>
                    
                    <form action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .form-control-static {
        padding: 0.375rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        min-height: 38px;
        display: flex;
        align-items: center;
    }
    
    .item-group {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Botón para imprimir
        $('.btn-print').click(function() {
            window.print();
        });
    });
</script>
@stop