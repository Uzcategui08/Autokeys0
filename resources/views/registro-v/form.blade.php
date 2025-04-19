<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="fecha_h" class="form-label">{{ __('Fecha H') }}</label>
                    <input type="date" name="fecha_h" class="form-control @error('fecha_h') is-invalid @enderror"
                            value="{{ old('fecha_h', isset($registroV->fecha_h) ? $registroV->fecha_h->format('Y-m-d') : '') }}"  
                            id="fecha_h" placeholder="Fecha H">
                    {!! $errors->first('fecha_h', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="tecnico" class="form-label">{{ __('Tecnico') }}</label>
                    <input type="text" name="tecnico" class="form-control @error('tecnico') is-invalid @enderror" 
                        value="{{ old('tecnico', $registroV?->tecnico) }}" id="tecnico" placeholder="Tecnico">
                    {!! $errors->first('tecnico', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="trabajo" class="form-label">{{ __('Trabajo') }}</label>
                    <input type="text" name="trabajo" class="form-control @error('trabajo') is-invalid @enderror" 
                        value="{{ old('trabajo', $registroV?->trabajo) }}" id="trabajo" placeholder="Trabajo">
                    {!! $errors->first('trabajo', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="valor_v" class="form-label">{{ __('Valor V') }}</label>
                    <input type="text" name="valor_v" class="form-control @error('valor_v') is-invalid @enderror" 
                        value="{{ old('valor_v', $registroV?->valor_v) }}" id="valor_v" placeholder="Valor V">
                    {!! $errors->first('valor_v', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
                    <select name="estatus" id="estatus" class="form-control @error('estatus') is-invalid @enderror">
                        <option value="">Seleccione un estado</option>
                        <option value="pagado" {{ old('estatus', $registroV?->estatus) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="pendiente" {{ old('estatus', $registroV?->estatus) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="parcialementep" {{ old('estatus', $registroV?->estatus) == 'parcialementep' ? 'selected' : '' }}>Parcialmente pagado</option>
                    </select>
                    {!! $errors->first('estatus', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="metodo_p" class="form-label">{{ __('Metodo P') }}</label>
                    <input type="text" name="metodo_p" class="form-control @error('metodo_p') is-invalid @enderror" 
                        value="{{ old('metodo_p', $registroV?->metodo_p) }}" id="metodo_p" placeholder="Metodo P">
                    {!! $errors->first('metodo_p', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="titular_c" class="form-label">{{ __('Titular C') }}</label>
                    <input type="text" name="titular_c" class="form-control @error('titular_c') is-invalid @enderror" 
                        value="{{ old('titular_c', $registroV?->titular_c) }}" id="titular_c" placeholder="Titular C">
                    {!! $errors->first('titular_c', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="cobro" class="form-label">{{ __('Cobro') }}</label>
                    <input type="text" name="cobro" class="form-control @error('cobro') is-invalid @enderror" 
                        value="{{ old('cobro', $registroV?->cobro) }}" id="cobro" placeholder="Cobro">
                    {!! $errors->first('cobro', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="descripcion_ce" class="form-label">{{ __('Descripcion Ce') }}</label>
                    <input type="text" name="descripcion_ce" class="form-control @error('descripcion_ce') is-invalid @enderror" 
                        value="{{ old('descripcion_ce', $registroV?->descripcion_ce) }}" id="descripcion_ce" placeholder="Descripcion Ce">
                    {!! $errors->first('descripcion_ce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="monto_ce" class="form-label">{{ __('Monto Ce') }}</label>
                    <input type="text" name="monto_ce" class="form-control @error('monto_ce') is-invalid @enderror" 
                        value="{{ old('monto_ce', $registroV?->monto_ce) }}" id="monto_ce" placeholder="Monto Ce">
                    {!! $errors->first('monto_ce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="metodo_pce" class="form-label">{{ __('Metodo Pce') }}</label>
                    <input type="text" name="metodo_pce" class="form-control @error('metodo_pce') is-invalid @enderror" 
                        value="{{ old('metodo_pce', $registroV?->metodo_pce) }}" id="metodo_pce" placeholder="Metodo Pce">
                    {!! $errors->first('metodo_pce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="porcentaje_c" class="form-label">{{ __('Porcentaje C') }}</label>
                    <input type="text" name="porcentaje_c" class="form-control @error('porcentaje_c') is-invalid @enderror" 
                        value="{{ old('porcentaje_c', $registroV?->porcentaje_c) }}" id="porcentaje_c" placeholder="Porcentaje C">
                    {!! $errors->first('porcentaje_c', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="marca" class="form-label">{{ __('Marca') }}</label>
                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                        value="{{ old('marca', $registroV?->marca) }}" id="marca" placeholder="Marca">
                    {!! $errors->first('marca', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="modelo" class="form-label">{{ __('Modelo') }}</label>
                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                        value="{{ old('modelo', $registroV?->modelo) }}" id="modelo" placeholder="Modelo">
                    {!! $errors->first('modelo', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <label for="año" class="form-label">{{ __('Año') }}</label>
                    <input type="text" name="año" class="form-control @error('año') is-invalid @enderror" 
                        value="{{ old('año', $registroV?->año) }}" id="año" placeholder="Año">
                    {!! $errors->first('año', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                </div>

                <div class="form-group mb-2 mb20">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="id_cliente" class="form-label">{{ __('Cliente') }}</label>
                            <select name="cliente" id="id_cliente" class="form-control @error('id_cliente') is-invalid @enderror">
                                <option value="">{{ __('Seleccione un cliente') }}</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->nombre }}"
                                    data-telefono="{{ $cliente->telefono }}"
                                    {{ old('cliente', $registroV?->cliente) == $cliente->nombre ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->apellido ?? '' }}
                                </option>
                                @endforeach
                            </select>
                            {!! $errors->first('cliente', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>

                        <div class="col-md-4">
                            <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
                            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                id="telefono" placeholder="Teléfono" readonly>
                            {!! $errors->first('telefono', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
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
                                    <span id="valor-total">${{ number_format($registroV->valor_v ?? 0, 2) }}</span>
                                </div>
                                <div>
                                    <strong>Total Pagado:</strong> 
                                    <span id="total-pagado">$0.00</span>
                                </div>
                                <div>
                                    <strong>Saldo Pendiente:</strong> 
                                    <span id="saldo-pendiente">${{ number_format($registroV->valor_v ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" id="pago_monto" class="form-control" placeholder="0.00">
                                <small class="text-muted">Monto máximo: <span id="maximo-pago">${{ number_format($registroV->valor_v ?? 0, 2) }}</span></small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Método de Pago</label>
                                <select id="pago_metodo" class="form-control">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" id="pago_fecha" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <button type="button" id="btn-agregar-pago" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Agregar Pago
                        </button>

                        <div class="mt-4" id="lista-pagos">
                            @if(!empty($registroV->pagos) && is_array($registroV->pagos))
                                @foreach($registroV->pagos as $index => $pago)
                                <div class="pago-item card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold">${{ number_format($pago['monto'], 2) }}</span>
                                                <span class="text-muted ms-2">({{ $pago['metodo_pago'] }})</span>
                                                <small class="text-muted ms-2">{{ $pago['fecha'] }}</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="{{ $index }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning">No hay pagos registrados</div>
                            @endif
                        </div>
                    </div>
                </div>

                <input type="hidden" name="pagos" id="pagos_json" value='@json(old('pagos', $registroV->pagos ?? []))'>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group mb-2 mb20" id="items-container">
                    <label for="items" class="form-label">{{ __('Items') }}</label>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button type="button" class="btn btn-success btn-add-work">{{ __('Agregar Trabajo') }}</button>
            </div>
            <div class="col-md-12 mt-3 text-center">
                <button type="submit" class="btn btn-secondary btn-lg px-5">{{ __('Grabar') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
.select2-container .select2-selection--single {
    height: 38px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
.pago-item {
transition: all 0.3s ease;
}
.pago-item:hover {
    background-color: #f8f9fa;
}
.btn-eliminar-pago {
    transition: all 0.2s ease;
}
.btn-eliminar-pago:hover {
    transform: scale(1.1);
}
#valor-total, #total-pagado, #saldo-pendiente {
    font-weight: bold;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let valorTotal = parseFloat($('#valor_v').val()) || 0;
    let totalPagado = 0;
    let saldoPendiente = valorTotal;

    actualizarResumen();

    $('#valor_v').on('change', function() {
        valorTotal = parseFloat($(this).val()) || 0;
        actualizarResumen();
        actualizarMaximoPago();
    });

    $('#estatus').on('change', function() {

        if(totalPagado === 0) {
            if($(this).val() === 'pagado') {

                agregarPagoCompleto();
            }
        }
    });

    function agregarPagoCompleto() {
        const pagosJson = JSON.stringify([{
            monto: valorTotal,
            metodo_pago: 'completo',
            fecha: new Date().toISOString().split('T')[0]
        }]);
        
        $('#pagos_json').val(pagosJson);
        actualizarListaPagos();
        actualizarResumen();
    }

    function actualizarResumen() {
        totalPagado = calcularTotalPagado();
        saldoPendiente = valorTotal - totalPagado;
        
        $('#valor-total').text('$' + valorTotal.toFixed(2));
        $('#total-pagado').text('$' + totalPagado.toFixed(2));
        $('#saldo-pendiente').text('$' + (saldoPendiente > 0 ? saldoPendiente.toFixed(2) : '0.00'));

        actualizarEstatus();
    }

    function actualizarMaximoPago() {
        const maxPago = saldoPendiente > 0 ? saldoPendiente : 0;
        $('#maximo-pago').text('$' + maxPago.toFixed(2));
        $('#pago_monto').attr('max', maxPago);
    }

    function calcularTotalPagado() {
        const pagosJson = $('#pagos_json').val() || '[]';
        try {
            const jsonStr = pagosJson.replace(/^"|"$/g, '');
            const pagos = JSON.parse(jsonStr);
            return pagos.reduce((total, pago) => total + parseFloat(pago.monto), 0);
        } catch (e) {
            return 0;
        }
    }

    function actualizarEstatus() {
        if (saldoPendiente <= 0.01) { 
            $('#estatus').val('pagado');
        } else if (totalPagado > 0) {
            $('#estatus').val('parcialementep'); 
        } else {
            $('#estatus').val('pendiente');
        }
    }

    $('#btn-agregar-pago').click(function() {
        const monto = parseFloat($('#pago_monto').val());
        const metodo = $('#pago_metodo').val();
        const fecha = $('#pago_fecha').val();
        
        if (!monto || monto <= 0) {
            alert('Error: Ingrese un monto válido mayor a cero');
            return;
        }
        
        if (monto > saldoPendiente) {
            alert('Error: El monto excede el saldo pendiente de $' + saldoPendiente.toFixed(2));
            return;
        }
        
        const pagosJson = $('#pagos_json').val() || '[]';
        let pagos = [];
        
        try {
            pagos = JSON.parse(pagosJson);
            if (!Array.isArray(pagos)) pagos = [];
        } catch (e) {
            console.error('Error parseando pagos:', e);
        }
        
        pagos.push({
            monto: monto,
            metodo_pago: metodo,
            fecha: fecha
        });
        
        $('#pagos_json').val(JSON.stringify(pagos));
        
        actualizarListaPagos();
        actualizarResumen();
        actualizarMaximoPago();
        
        $('#pago_monto').val('').focus();
    });

    function actualizarListaPagos() {
        const pagosJson = $('#pagos_json').val() || '[]';
        $('#lista-pagos').empty();
        
        try {
            const pagos = JSON.parse(pagosJson);
            
            if (pagos.length === 0) {
                $('#lista-pagos').html('<div class="alert alert-warning">No hay pagos registrados</div>');
                return;
            }
            
            pagos.forEach((pago, index) => {
                $('#lista-pagos').append(`
                    <div class="pago-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">$${parseFloat(pago.monto).toFixed(2)}</span>
                                    <span class="text-muted ms-2">(${pago.metodo_pago})</span>
                                    <small class="text-muted ms-2">${pago.fecha}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
        } catch (e) {
            console.error('Error mostrando pagos:', e);
            $('#lista-pagos').html('<div class="alert alert-danger">Error mostrando los pagos</div>');
        }
    }

    $(document).on('click', '.btn-eliminar-pago', function() {
        const index = $(this).data('index');
        const pagosJson = $('#pagos_json').val() || '[]';
        
        try {
            let pagos = JSON.parse(pagosJson);
            
            if (index >= 0 && index < pagos.length) {
                pagos.splice(index, 1);
                
                $('#pagos_json').val(JSON.stringify(pagos));
                actualizarListaPagos();
                actualizarResumen();
                actualizarMaximoPago();
            }
        } catch (e) {
            console.error('Error eliminando pago:', e);
        }
    });

    actualizarListaPagos();
    actualizarMaximoPago();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('id_cliente');
    const telefonoInput = document.getElementById('telefono');

    // Función para actualizar el teléfono
    function actualizarTelefono() {
        if (clienteSelect.value) {
            const selectedOption = clienteSelect.options[clienteSelect.selectedIndex];
            telefonoInput.value = selectedOption.dataset.telefono || '';
        } else {
            telefonoInput.value = '';
        }
    }

    // Cargar teléfono inicial si hay cliente seleccionado
    actualizarTelefono();

    // Escuchar cambios en el select
    clienteSelect.addEventListener('change', actualizarTelefono);
});

</script>

<script>
$(document).ready(function() {
    let itemGroupIndex = 0;
    const itemsExistentes = @json($registroV->items ?? []);

    function cargarProductosEnSelect($select, idAlmacen, productoSeleccionado = null, nombreProducto = null) {
        if (idAlmacen) {
            $.ajax({
                url: '/obtener-productos-orden',
                type: 'GET',
                data: { id_almacen: idAlmacen },
                success: function(response) {

                    let options = '<option value="">{{ __('Select Producto') }}</option>';
                    
                    if (productoSeleccionado) {
                        const productoEncontrado = response.find(p => p.id_producto == productoSeleccionado);
                        
                        if (productoEncontrado) {
                            options += `
                                <option value="${productoEncontrado.id_producto}" selected>
                                    ${productoEncontrado.id_producto} - ${productoEncontrado.item}
                                </option>`;
                        } else if (nombreProducto) {
                            options += `
                                <option value="${productoSeleccionado}" selected>
                                    ${productoSeleccionado} - ${nombreProducto}
                                </option>`;
                        }
                    }
                    
                    response.forEach(function(producto) {
                        if (producto.id_producto != productoSeleccionado) {
                            options += `
                                <option value="${producto.id_producto}">
                                    ${producto.id_producto} - ${producto.item}
                                </option>`;
                        }
                    });
                    
                    $select.html(options).prop('disabled', false);

                    $select.select2();
                    if (productoSeleccionado) {
                        $select.val(productoSeleccionado).trigger('change');
                    }

                },
                error: function(xhr) {
                    console.error('Error al cargar los productos');
                    if (productoSeleccionado && nombreProducto) {
                        $select.html(`
                            <option value="${productoSeleccionado}" selected>
                                ${productoSeleccionado} - ${nombreProducto}
                            </option>
                            <option value="">{{ __('Select Producto') }}</option>
                        `).prop('disabled', false);
                        $select.select2();
                    }
                }
            });
        } else {
            if (productoSeleccionado && nombreProducto) {
                $select.html(`
                    <option value="${productoSeleccionado}" selected>
                        ${productoSeleccionado} - ${nombreProducto}
                    </option>
                    <option value="">{{ __('Select Producto') }}</option>
                `).prop('disabled', false);
                $select.select2();
            } else {
                $select.html('<option value="">{{ __('Select Producto') }}</option>').prop('disabled', true);
            }
        }
    }


   function addNewProductRow(itemGroup, productoData = null) {
        const productoIndex = itemGroup.find('.producto-row').length;
        const itemGroupIndex = itemGroup.data('index');

        const newProductoRow = `
            <div class="row mb-2 producto-row">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Producto') }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][producto]"
                            class="form-control select2-producto" ${productoData ? '' : 'disabled'}>
                        <option value="">{{ __('Select Producto') }}</option>
                        ${productoData ? `
                            <option value="${productoData.producto}" selected>
                                ${productoData.producto} - ${productoData.nombre_producto || 'Producto'}
                            </option>
                        ` : ''}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Cantidad') }}</label>
                    <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][cantidad]"
                           class="form-control" placeholder="Cantidad" min="1"
                           value="${productoData ? productoData.cantidad : ''}">
                </div>
                <div class="col-md-2">
                <label class="form-label">{{ __('Precio') }}</label>
                <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][precio]"
                        class="form-control" placeholder="Precio" min="1"
                        value="${productoData ? productoData.precio : ''}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Almacén') }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][almacen]"
                            class="form-control select-almacen">
                        <option value="">{{ __('Select Almacén') }}</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id_almacen }}"
                                ${productoData && productoData.almacen == '{{ $almacen->id_almacen }}' ? 'selected' : ''}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-remove-producto mt-4">-</button>
                </div>
            </div>
        `;

        itemGroup.find('.productos-container').append(newProductoRow);
        const $newRow = itemGroup.find('.producto-row').last();

        if (productoData) {
            const $selectAlmacen = $newRow.find('.select-almacen');
            const $selectProducto = $newRow.find('.select2-producto');

            cargarProductosEnSelect(
                $selectProducto,
                productoData.almacen,
                productoData.producto,
                productoData.nombre_producto,
                productoData.cantidad
            );
        }
    }

    function addNewItemGroup(itemData = null) {
        const currentIndex = itemGroupIndex;
        const newItemGroup = $(`
            <div class="item-group mb-4 p-3 border rounded" data-index="${currentIndex}">
                <div class="row mb-2">
                    <div class="col-md-11">
                        <label class="form-label">{{ __('Trabajo') }}</label>
                        <textarea name="items[${currentIndex}][trabajo]" class="form-control" placeholder="Descripción del trabajo">${itemData ? (itemData.trabajo || '') : ''}</textarea>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-item-group mt-4">×</button>
                    </div>
                </div>
                <div class="productos-container"></div>
                <button type="button" class="btn btn-success btn-add-producto mt-2">{{ __('Agregar Producto') }}</button>
            </div>
        `);

        $('#items-container').append(newItemGroup);

        if (itemData && itemData.productos) {
            itemData.productos.forEach(producto => {
                addNewProductRow(newItemGroup, producto);
            });
        }
        
        itemGroupIndex++;
    }

    $(document).on('click', '.btn-add-work', function() {
        addNewItemGroup();
    });

    $(document).on('click', '.btn-add-producto', function() {
        const itemGroup = $(this).closest('.item-group');
        addNewProductRow(itemGroup);
    });

    $(document).on('click', '.btn-remove-producto', function() {
        $(this).closest('.producto-row').remove();
    });

    $(document).on('click', '.btn-remove-item-group', function() {
        $(this).closest('.item-group').remove();
    });

    $(document).on('change', 'select[name^="items"][name$="[almacen]"]', function() {
        const $row = $(this).closest('.producto-row');
        const $selectProducto = $row.find('.select2-producto');
        const productoSeleccionado = $selectProducto.val();
        const nombreProducto = $selectProducto.find('option:selected').text().split('-')[1]?.trim();
        
        cargarProductosEnSelect(
            $selectProducto, 
            $(this).val(), 
            productoSeleccionado,
            nombreProducto
        );
    });

    if (itemsExistentes && itemsExistentes.length > 0) {
        itemsExistentes.forEach(item => {
            addNewItemGroup(item);
        });
        
        $('.item-group').each(function() {
            const itemGroupIndex = $(this).data('index');
            const itemData = itemsExistentes[itemGroupIndex];
            
            if (itemData && itemData.productos) {
                $(this).find('.producto-row').each(function(index) {
                    const productoData = itemData.productos[index];
                    if (productoData) {
                        const $selectProducto = $(this).find('.select2-producto');
                        const $selectAlmacen = $(this).find('.select-almacen');
                        
                        cargarProductosEnSelect(
                            $selectProducto,
                            productoData.almacen,
                            productoData.producto,
                            productoData.nombre_producto,
                            productoData.cantidad
                        );
                    }
                });
            }
        });
    } else {
        addNewItemGroup();
    }
});
</script>

