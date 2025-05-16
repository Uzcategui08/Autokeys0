@extends('layouts.app')

@section('titulo', 'Editar Inventario')

@section('contenido')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary">
                    <h4 class="mb-0 text-white">
                        <i class="fas fa-boxes me-2"></i>Editar Registro de Inventario
                    </h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('inventarios.update', $inventario->id_inventario) }}">
                        @csrf
                        @method('PUT')

                        <!-- Información Básica -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">ID Inventario</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="INV-{{ str_pad($inventario->id_inventario, 5, '0', STR_PAD_LEFT) }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Cantidad Actual</label>
                                    <input type="number" class="form-control bg-light" 
                                           value="{{ $inventario->cantidad }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Última Actualización</label>
                                    <input type="text" class="form-control bg-light" 
                                           value="{{ $inventario->updated_at->format('d/m/Y H:i') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Selección de Producto y Almacén -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="id_producto" name="id_producto" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id_producto }}" 
                                                {{ $inventario->id_producto == $producto->id_producto ? 'selected' : '' }}
                                                data-stock="{{ $producto->stock }}">
                                                {{ $producto->item }} ({{ $producto->codigo }}) - Stock: {{ $producto->stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_almacen" class="form-label">Almacén <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="id_almacen" name="id_almacen" required>
                                        <option value="">Seleccione un almacén</option>
                                        @foreach($almacenes as $almacen)
                                            <option value="{{ $almacen->id_almacen }}" 
                                                {{ $inventario->id_almacen == $almacen->id_almacen ? 'selected' : '' }}>
                                                {{ $almacen->nombre }} ({{ $almacen->ubicacion }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Ajuste de Cantidad -->
                        <div class="card mb-4">
                            <div class="card-header bg-gradient-info text-white">
                                <h5 class="mb-0">Ajuste de Inventario</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tipo de Movimiento</label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="tipo_movimiento" id="entrada" value="entrada" checked>
                                                <label class="btn btn-outline-success" for="entrada">
                                                    <i class="fas fa-plus-circle me-1"></i> Entrada
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="tipo_movimiento" id="salida" value="salida">
                                                <label class="btn btn-outline-danger" for="salida">
                                                    <i class="fas fa-minus-circle me-1"></i> Salida
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="tipo_movimiento" id="ajuste" value="ajuste">
                                                <label class="btn btn-outline-warning" for="ajuste">
                                                    <i class="fas fa-exchange-alt me-1"></i> Ajuste
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="cantidad" 
                                                   name="cantidad" min="1" value="1" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nueva_cantidad">Nueva Cantidad</label>
                                            <input type="number" class="form-control bg-light" id="nueva_cantidad" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="motivo">Motivo del Ajuste</label>
                                    <textarea class="form-control" id="motivo" name="motivo" rows="2" 
                                              placeholder="Ej: Ajuste por inventario físico, entrada de compra, etc."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between pt-2">
                            <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Regresar
                            </a>
                            
                            <div>
                                <button type="reset" class="btn btn-default">
                                    <i class="fas fa-undo me-1"></i> Restablecer
                                </button>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Calcular nueva cantidad automáticamente
        function calcularNuevaCantidad() {
            const cantidadActual = {{ $inventario->cantidad }};
            const cantidadAjuste = parseInt($('#cantidad').val()) || 0;
            const tipoMovimiento = $('input[name="tipo_movimiento"]:checked').val();
            
            let nuevaCantidad = cantidadActual;
            
            if (tipoMovimiento === 'entrada') {
                nuevaCantidad = cantidadActual + cantidadAjuste;
            } else if (tipoMovimiento === 'salida') {
                nuevaCantidad = cantidadActual - cantidadAjuste;
            } else if (tipoMovimiento === 'ajuste') {
                nuevaCantidad = cantidadAjuste;
            }
            
            $('#nueva_cantidad').val(nuevaCantidad);
            
            // Cambiar color según el tipo de movimiento
            if (nuevaCantidad > cantidadActual) {
                $('#nueva_cantidad').removeClass('bg-danger bg-warning').addClass('bg-success text-white');
            } else if (nuevaCantidad < cantidadActual) {
                $('#nueva_cantidad').removeClass('bg-success bg-warning').addClass('bg-danger text-white');
            } else {
                $('#nueva_cantidad').removeClass('bg-success bg-danger').addClass('bg-warning text-dark');
            }
        }
        
        // Escuchar cambios
        $('input[name="tipo_movimiento"]').change(calcularNuevaCantidad);
        $('#cantidad').on('input', calcularNuevaCantidad);
        
        // Calcular al cargar la página
        calcularNuevaCantidad();
    });
</script>
@endsection