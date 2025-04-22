@extends('adminlte::page')

@section('title', 'Registro de Ventas')

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
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Detalles del Registro</h2>
            <a href="{{ route('registro-vs.index') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row padding-1 p-1">
        <div class="col-md-12">
            
            <!-- Sección 1: Información del Trabajo y Vehículo -->
            <div class="row mb-4">
                <!-- Columna Izquierda - Información del Trabajo -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Información del Trabajo</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Fecha De Ejecución') }}</label>
                                <p class="form-control-static">{{ $registroV->fecha_h->format('d/m/Y') }}</p>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Técnico') }}</label>
                                <p class="form-control-static">{{ $registroV->tecnico }}</p>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Lugar de Venta') }}</label>
                                <p class="form-control-static">{{ $registroV->lugarventa }}</p>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Trabajo') }}</label>
                                <p class="form-control-static">{{ ucfirst($registroV->trabajo) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Información del Vehículo -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Información del Vehículo</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Marca') }}</label>
                                <p class="form-control-static">{{ $registroV->marca }}</p>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Modelo') }}</label>
                                <p class="form-control-static">{{ $registroV->modelo }}</p>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('Año') }}</label>
                                <p class="form-control-static">{{ $registroV->año }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Items de Trabajo -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Items de Trabajo</h5>
                        </div>
                        <div class="card-body">
                            @if($registroV->items && count($registroV->items) > 0)
                                @foreach($registroV->items as $item)
                                    <div class="item-group mb-4 p-3 border rounded">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <label class="form-label">{{ __('Trabajo') }}</label>
                                                <p class="form-control-static">{{ $item['trabajo'] ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        @if(isset($item['productos']) && count($item['productos']) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Almacén</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($item['productos'] as $producto)
                                                            <tr>
                                                                <td>{{ $producto['producto'] }} - {{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                                                                <td>{{ $producto['cantidad'] }}</td>
                                                                <td>${{ number_format($producto['precio'], 2) }}</td>
                                                                <td>
                                                                    @php
                                                                        $almacen = $almacenes->firstWhere('id_almacen', $producto['almacen']);
                                                                        echo $almacen ? $almacen->nombre : 'N/A';
                                                                    @endphp
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">No hay productos registrados para este trabajo</div>
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

            <!-- Sección 3: Información del Cliente -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Información del Cliente</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="form-label">{{ __('Cliente') }}</label>
                                        <p class="form-control-static">{{ $registroV->cliente }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Teléfono') }}</label>
                                        <p class="form-control-static">{{ $registroV->telefono }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Costos Extras -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Costos Extras</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Descripción') }}</label>
                                        <p class="form-control-static">{{ $registroV->descripcion_ce ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Monto') }}</label>
                                        <p class="form-control-static">${{ number_format($registroV->monto_ce ?? 0, 2) }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Cobro') }}</label>
                                        <p class="form-control-static">${{ $registroV->cobro ?? N/A }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('% Cerrajero') }}</label>
                                        <p class="form-control-static">${{ number_format($registroV->porcentaje_c ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 5: Información de Pago -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Información de Pago</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Valor') }}</label>
                                        <p class="form-control-static">${{ number_format($registroV->valor_v, 2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Titular') }}</label>
                                        <p class="form-control-static">{{ $registroV->titular_c ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                                            
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Estatus') }}</label>
                                        <p class="form-control-static">
                                            @switch($registroV->estatus)
                                                @case('pagado')
                                                    <span class="badge bg-success">Pagado</span>
                                                    @break
                                                @case('pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                    @break
                                                @case('parcialementep')
                                                    <span class="badge bg-info">Parcialmente Pagado</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $registroV->estatus }}</span>
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 6: Registro de Pagos Parciales -->
            @if($registroV->pagos && count($registroV->pagos) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Registro de Pagos Parciales</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-4">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Valor Total:</strong> 
                                        <span>${{ number_format($registroV->valor_v, 2) }}</span>
                                    </div>
                                    <div>
                                        @php
                                            $totalPagado = collect($registroV->pagos)->sum('monto');
                                        @endphp
                                        <strong>Total Pagado:</strong> 
                                        <span>${{ number_format($totalPagado, 2) }}</span>
                                    </div>
                                    <div>
                                        <strong>Saldo Pendiente:</strong> 
                                        <span>${{ number_format($registroV->valor_v - $totalPagado, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
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
                                            <td>{{ ucfirst($pago['metodo_pago']) }}</td>
                                            <td>{{ $pago['fecha'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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