<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="id_presupuesto" class="form-label">{{ __('Id Presupuesto') }}</label>
                    <input type="text" name="id_presupuesto"
                           class="form-control @error('id_presupuesto') is-invalid @enderror"
                           value="{{ old('id_presupuesto', $presupuesto?->id_presupuesto) }}" id="id_presupuesto"
                           placeholder="Id Presupuesto">
                    {!! $errors->first('id_presupuesto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="id_cliente" class="form-label">{{ __('Id Cliente') }}</label>
                    <select name="id_cliente"
                            class="form-control select2 @error('id_cliente') is-invalid @enderror" id="id_cliente"
                            style="height: 38px !important;">
                        <option value="">{{ __('Select Cliente') }}</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', $presupuesto?->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="f_presupuesto" class="form-label">{{ __('F Presupuesto') }}</label>
                    <input type="date" name="f_presupuesto"
                           class="form-control @error('f_presupuesto') is-invalid @enderror"
                           value="{{ old('f_presupuesto', $presupuesto?->f_presupuesto) }}" id="f_presupuesto">
                    {!! $errors->first('f_presupuesto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="validez" class="form-label">{{ __('Validez') }}</label>
                    <input type="date" name="validez" class="form-control @error('validez') is-invalid @enderror"
                           value="{{ old('validez', $presupuesto?->validez) }}" id="validez">
                    {!! $errors->first('validez', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="descuento" class="form-label">{{ __('Descuento') }}</label>
                    <div class="input-group">
                        <input type="number" name="descuento"
                               class="form-control @error('descuento') is-invalid @enderror"
                               value="{{ old('descuento', $presupuesto?->descuento) }}" id="descuento"
                               placeholder="Descuento" min="0" max="100">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    {!! $errors->first('descuento', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="iva" class="form-label">{{ __('Iva') }}</label>
                    <select name="iva" class="form-control @error('iva') is-invalid @enderror" id="iva">
                        <option value="0" {{ old('iva', $presupuesto?->iva) == 0 ? 'selected' : '' }}>0%</option>
                        <option value="10" {{ old('iva', $presupuesto?->iva) == 10 ? 'selected' : '' }}>10%</option>
                        <option value="21" {{ old('iva', $presupuesto?->iva) == 21 ? 'selected' : '' }}>21%</option>
                    </select>
                    {!! $errors->first('iva', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="estado" class="form-label">{{ __('Estado') }}</label>
                    <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado">
                        <option value="pendiente" {{ old('estado', $presupuesto?->estado) == 'pendiente' ? 'selected' : '' }}>
                            Pendiente
                        </option>
                        <option value="aprobado" {{ old('estado', $presupuesto?->estado) == 'aprobado' ? 'selected' : '' }}>
                            Aprobado
                        </option>
                        <option value="rechazado" {{ old('estado', $presupuesto?->estado) == 'rechazado' ? 'selected' : '' }}>
                            Rechazado
                        </option>
                    </select>
                    {!! $errors->first('estado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>

        <div class="form-group mb-2 mb20" id="items-container">
            <label for="items" class="form-label">{{ __('Items') }}</label>

        </div>

        <div class="col-md-12 mt-4 text-center">
            <button type="button" class="btn btn-success btn-add-work">{{ __('Agregar Trabajo') }}</button>
        </div>

        <div class="col-md-12 mt-4 text-center">
            <button type="submit" class="btn btn-secondary btn-lg px-5">{{ __('Grabar') }}</button>
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
    $(document).ready(function() {
        let itemGroupIndex = 0;
        const itemsExistentes = @json($presupuesto->items ?? []);

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