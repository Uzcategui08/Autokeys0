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
            <div class="col-md-3">
                <div class="form-group mb-2 mb20">
                    <label for="almacen" class="form-label">{{ __('Almacén') }}</label>
                    <select name="almacen" id="almacen" class="form-control">
                        <option value="">{{ __('Select Almacén') }}</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id_almacen }}" {{ old('almacen', $presupuesto?->almacen) == $almacen->id_almacen ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mb-2 mb20">
            <label for="items" class="form-label">{{ __('Items') }}</label>
            <div id="items-container">
                @if (isset($presupuesto->items) && count($presupuesto->items) > 0)
                    @foreach ($presupuesto->items as $index => $item)
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <select name="items[{{ $index }}][producto]"
                                        class="form-control select2-producto" {{ !$presupuesto->almacen ? 'disabled' : '' }}>
                                    <option value="">{{ __('Select Producto') }}</option>
                                    @foreach($inventario as $producto)
                                        <option value="{{ $producto->id_producto }}" {{ $item['producto'] == $producto->id_producto ? 'selected' : '' }}>
                                            {{ $producto->id_producto }} - {{ $item['nombre_producto'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="items[{{ $index }}][cantidad]"
                                       class="form-control" placeholder="Cantidad" min="1"
                                       value="{{ old("items.$index.cantidad", $item['cantidad']) }}">
                            </div>
                            <input type="hidden" name="items[{{ $index }}][almacen]"
                                   value="{{ old("items.$index.almacen", $item['almacen'] ?? '') }}">
                            <div class="col-md-2">
                                @if ($index == 0)
                                    <button type="button" class="btn btn-success btn-add-item">+</button>
                                @else
                                    <button type="button" class="btn btn-danger btn-remove-item">-</button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                @else
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select name="items[0][producto]" class="form-control select2-producto" disabled>
                                <option value="">{{ __('Select Producto') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="items[0][cantidad]" class="form-control" placeholder="Cantidad"
                                   min="1">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success btn-add-item">+</button>
                        </div>
                    </div>
                @endif
            </div>
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
    let itemIndex = $('#items-container .row').length;

    function actualizarEstadoProductos() {
        const idAlmacen = $('#almacen').val();
        const productosHabilitados = idAlmacen !== "";

        $('select[name^="items"]').each(function() {
            const $select = $(this);
            if (!productosHabilitados) {
                $select.prop('disabled', true);
            } else {
                $select.prop('disabled', false);
                if (!$select.val()) {
                    cargarProductosEnSelect($select);
                }
            }
        });
    }

    function cargarProductosEnSelect($select) {
        const idAlmacen = $('#almacen').val();

        if (idAlmacen) {
            $.ajax({
                url: '/obtener-productos',
                type: 'GET',
                data: {
                    id_almacen: idAlmacen
                },
                success: function(response) {
                    $select.empty().append('<option value="">{{ __('Select Producto') }}</option>');
                    response.forEach(function(producto) {
                        $select.append(
                            `<option value="${producto.id_producto}">${producto.id_producto} - ${producto.item}</option>`
                        );
                    });
                },
                error: function(xhr) {
                    console.error('Error al cargar los productos');
                }
            });
        }
    }

    $(document).on('click', '.btn-add-item', function() {
        const newItem = `
            <div class="row mb-2">
                <div class="col-md-6">
                    <select name="items[${itemIndex}][producto]" class="form-control select2-producto">
                        <option value="">{{ __('Select Producto') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[${itemIndex}][cantidad]" class="form-control" placeholder="Cantidad" min="1">
                </div>
                <input type="hidden" name="items[${itemIndex}][almacen]" value="${$('#almacen').val()}">
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove-item">-</button>
                </div>
            </div>
        `;
        $('#items-container').append(newItem);

        const $newSelect = $('select[name="items[' + itemIndex + '][producto]"]');
        $newSelect.select2();

        actualizarEstadoProductos();

        if ($('#almacen').val()) {
            cargarProductosEnSelect($newSelect);
        }

        itemIndex++;
    });

    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('.row').remove();
    });

    $('#almacen').change(function() {
        const idAlmacen = $(this).val();

        if (idAlmacen) {
            actualizarEstadoProductos();

            $('select[name^="items"]').each(function() {
                const $select = $(this);
                if (!$select.val()) {
                    cargarProductosEnSelect($select);
                }
            });
        } else {
            actualizarEstadoProductos();
        }
    });

    actualizarEstadoProductos();

    if ($('#almacen').val()) {
        actualizarEstadoProductos();
    }

    $('form').on('submit', function() {
        $(':disabled').prop('disabled', false);
    });
});
</script>