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

                <!-- Sección de Información del Trabajo y Vehículo -->
                <div class="row invoice-info">
                    <!-- Información del Trabajo -->
                    <div class="col-sm-4 invoice-col">
                        <strong>Técnico</strong>
                        <address>
                            {{ $registroV->tecnico }}<br>
                            Trabajo: {{ $registroV->trabajo }}<br>
                            Estatus: <span class="badge 
                                @if($registroV->estatus == 'pagado') badge-success 
                                @elseif($registroV->estatus == 'parcialemente pagado') badge-warning 
                                @elseif($registroV->estatus == 'pendiente') badge-danger 
                                @else badge-secondary 
                                @endif">
                                {{ ucfirst($registroV->estatus) }}
                            </span>
                        </address>
                    </div>

                    <!-- Información del Vehículo -->
                    <div class="col-sm-4 invoice-col">
                        <strong>Vehículo</strong>
                        <address>
                            Marca: {{ $registroV->marca }}<br>
                            Modelo: {{ $registroV->modelo }}<br>
                            Año: {{ $registroV->año }}
                        </address>
                    </div>

                    <!-- Información de Pago -->
                    <div class="col-sm-4 invoice-col">
                        <b>Registro V #{{ $registroV->id }}</b><br>
                        <b>Valor Total:</b> ${{ number_format($registroV->valor_v, 2) }}<br>
                        <b>Cobro:</b> ${{ ($registroV->cobro) }}<br>
                        <b>Titular:</b> {{ $registroV->titular_c }}
                    </div>
                </div>

                <!-- Sección de Cliente -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Información del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Cliente</label>
                                            <p class="form-control-static">{{ $registroV->cliente }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <p class="form-control-static">{{ $registroV->telefono }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Items de Trabajo -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Items de Trabajo</h5>
                            </div>
                            <div class="card-body">
                                @if(!empty($registroV->items) && count($registroV->items) > 0)
                                    @foreach($registroV->items as $itemGroup)
                                        <div class="item-group mb-3 p-3 border rounded">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Descripción del Trabajo</label>
                                                <p class="form-control-static">{{ $itemGroup['trabajo'] ?? 'N/A' }}</p>
                                            </div>
                                            
                                            @if(!empty($itemGroup['productos']))
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Producto</th>
                                                                <th>Cantidad</th>
                                                                <th>Precio</th>
                                                                <th>Almacén</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($itemGroup['productos'] as $producto)
                                                                <tr>
                                                                    <td>{{ $producto['producto'] ?? 'N/A' }}</td>
                                                                    <td>{{ $producto['cantidad'] ?? '0' }}</td>
                                                                    <td>${{ number_format($producto['precio'] ?? 0, 2) }}</td>
                                                                    <td>
                                                                        @foreach($almacenes as $almacen)
                                                                            @if($almacen->id_almacen == $producto['almacen'])
                                                                                {{ $almacen->nombre }}
                                                                            @endif
                                                                        @endforeach
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">No hay productos registrados para este trabajo</div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning">No hay items de trabajo registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Costos Extras -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Costos Extras</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Descripción</label>
                                            <p class="form-control-static">{{ $registroV->descripcion_ce ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Monto</label>
                                            <p class="form-control-static">${{ number_format($registroV->monto_ce, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Método de Pago</label>
                                            <p class="form-control-static">
                                                @foreach($tiposDePago as $tipo)
                                                    @if($tipo->id == $registroV->metodo_pce)
                                                        {{ $tipo->name }}
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">% Cerrajero</label>
                                            <p class="form-control-static">{{ $registroV->porcentaje_c }}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Pagos -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Registro de Pagos</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Valor Total:</strong> 
                                            <span>${{ number_format($registroV->valor_v, 2) }}</span>
                                        </div>
                                        <div>
                                            <strong>Total Pagado:</strong> 
                                            <span>${{ number_format($totalPagado = array_reduce($registroV->pagos ?? [], function($carry, $pago) { return $carry + $pago['monto']; }, 0), 2) }}</span>
                                        </div>
                                        <div>
                                            <strong>Saldo Pendiente:</strong> 
                                            <span>${{ number_format($registroV->valor_v - $totalPagado, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($registroV->pagos) && count($registroV->pagos) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Monto</th>
                                                    <th>Método de Pago</th>
                                                    <th>Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($registroV->pagos as $pago)
                                                    <tr>
                                                        <td>${{ number_format($pago['monto'], 2) }}</td>
                                                        <td>
                                                            @foreach($tiposDePago as $tipo)
                                                                @if($tipo->name == $pago['metodo_pago'])
                                                                    {{ $tipo->name }}
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $pago['fecha'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">No hay pagos registrados</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row no-print">
                    <div class="col-12">
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
                        
                        <a href="{{ route('registro-vs.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
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
    
    .invoice {
        position: relative;
        background: #fff;
        border: 1px solid #f4f4f4;
    }
    
    .invoice-title {
        margin-top: 0;
    }
    
    @media print {
        .no-print {
            display: none;
        }
        
        body {
            font-size: 11pt;
            margin: 0;
            padding: 0;
            background: none;
        }
        
        .container-fluid {
            width: 100%;
            padding: 0;
        }
        
        .card, .card-header, .card-body {
            border: none;
            box-shadow: none;
        }
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