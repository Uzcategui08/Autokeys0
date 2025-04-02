<div class="row padding-1 p-1">
    <div class="col-md-12">
        <!-- Primera Fila -->
        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="fecha_h" class="form-label">{{ __('Fecha H') }}</label>
                    <input type="text" name="fecha_h" class="form-control @error('fecha_h') is-invalid @enderror"
                        value="{{ old('fecha_h', $registroV?->fecha_h ?? now()->toDateString()) }}"
                        id="fecha_h" placeholder="Fecha H" readonly>
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

            <!-- Columna Derecha -->
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

        <!-- Segunda Fila -->
        <div class="row">
            <!-- Columna Izquierda -->
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

            <!-- Columna Derecha -->
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
        <!-- Select de Cliente (ocupará 8 columnas) -->
        <div class="col-md-8">
            <label for="id_cliente" class="form-label">{{ __('Cliente') }}</label>
            <select name="cliente" id="id_cliente" class="form-control @error('id_cliente') is-invalid @enderror">
                <option value="">{{ __('Seleccione un cliente') }}</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->id_cliente }}"
                    data-telefono="{{ $cliente->telefono }}"
                    {{ old('id_cliente', $registroV?->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>
                    {{ $cliente->nombre }} {{ $cliente->apellido ?? '' }}
                </option>
                @endforeach
            </select>
            {!! $errors->first('id_cliente', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>
        
        <!-- Input de Teléfono (ocupará 4 columnas) -->
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

        <!-- Área de Items -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-2 mb20" id="items-container">
                    <label for="items" class="form-label">{{ __('Items') }}</label>
                </div>
            </div>
        </div>

        <!-- Botón de Submit -->
        <div class="row">
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
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
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    const itemsExistentes = @json($orden->items ?? []);

    function cargarProductosEnSelect($select, idAlmacen, productoSeleccionado = null, nombreProducto = null) {
        if (idAlmacen) {
            $.ajax({
                url: '/obtener-productos',
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
                <div class="col-md-5">
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
                <div class="col-md-3">
                    <label class="form-label">{{ __('Cantidad') }}</label>
                    <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][cantidad]"
                           class="form-control" placeholder="Cantidad" min="1"
                           value="${productoData ? productoData.cantidad : ''}">
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
                productoData.nombre_producto
            );
        }
    }

    function addNewItemGroup(itemData = null) {
        const currentIndex = itemGroupIndex;
        const newItemGroup = $(`
            <div class="item-group"data-index="${currentIndex}">
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
                            productoData.nombre_producto
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

