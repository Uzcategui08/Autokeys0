<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Nueva Transferencia
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="id_producto" class="form-label fw-bold">Producto</label>
                                <select name="id_producto" id="id_producto" class="form-select select2" required>
                                    <option value="">Seleccione un producto</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id_producto }}" {{ old('id_producto') == $producto->id_producto ? 'selected' : '' }}>
                                            {{ $producto->item }} - {{ $producto->sku }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="id_almacen_origen" class="form-label fw-bold">Almacén Origen</label>
                                <select name="id_almacen_origen" id="id_almacen_origen" class="form-select select2" required>
                                    <option value="">Seleccione almacén</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id_almacen }}" {{ old('id_almacen_origen') == $almacen->id_almacen ? 'selected' : '' }}>
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="id_almacen_destino" class="form-label fw-bold">Almacén Destino</label>
                                <select name="id_almacen_destino" id="id_almacen_destino" class="form-select select2" required>
                                    <option value="">Seleccione almacén</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id_almacen }}" {{ old('id_almacen_destino') == $almacen->id_almacen ? 'selected' : '' }}>
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" required min="1" value="{{ old('cantidad') }}">
                        <small class="form-text text-muted">Cantidad mínima: 1 unidad</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3" placeholder="Observaciones...">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Realizar Transferencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<style>
    .card-title { font-size: 1.3rem; }
    .form-label { font-weight: 600; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function verificarStock() {
        const productoId = $('#id_producto').val();
        const almacenId = $('#id_almacen_origen').val();
        const cantidad = parseInt($('#cantidad').val()) || 0;
    
        let valido = true;
        let mensajeError = '';
    
        if (!productoId || !almacenId) {
            mensajeError = 'Seleccione producto y almacén de origen';
            valido = false;
            return { valido: valido, mensaje: mensajeError };
        }
    
        if (cantidad <= 0) {
            mensajeError = 'La cantidad debe ser mayor a cero';
            valido = false;
            return { valido: valido, mensaje: mensajeError };
        }
    
        try {
            const response = $.ajax({
                url: '/verificar-stock-transferencia',
                type: 'GET',
                async: false,
                data: {
                    producto_id: productoId,
                    almacen_id: almacenId,
                    cantidad: cantidad
                }
            }).responseJSON;
    
            if (!response.suficiente) {
                valido = false;
                mensajeError = `No hay suficiente stock. Disponible: ${response.stock}`;
            }
        } catch (error) {
            valido = false;
            mensajeError = 'Error al verificar el stock';
        }
    
        return {
            valido: valido,
            mensaje: mensajeError
        };
    }
    
    $(document).ready(function() {
        $('#id_producto, #id_almacen_origen').change(function() {
            const productoId = $('#id_producto').val();
            const almacenId = $('#id_almacen_origen').val();
            
            if (productoId && almacenId) {
                $.get('/verificar-stock-transferencia', {
                    producto_id: productoId,
                    almacen_id: almacenId,
                    cantidad: 0
                }, function(response) {
                    $('#stockDisponible').text(`Stock disponible: ${response.stock}`);
                }).fail(function() {
                    $('#stockDisponible').text('Error al cargar stock');
                });
            }
        });

        $('#transferenciaForm').on('submit', function(e) {
            e.preventDefault();
            
            const verificacion = verificarStock();
            
            if (!verificacion.valido) {
                Swal.fire({
                    title: 'Error',
                    text: verificacion.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }

            this.submit();
        });
    });
    </script>