<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="fecha_h" class="form-label">{{ __('Fecha H') }}</label>
            <input type="text" name="fecha_h" class="form-control @error('fecha_h') is-invalid @enderror" value="{{ old('fecha_h', $registroV?->fecha_h) }}" id="fecha_h" placeholder="Fecha H">
            {!! $errors->first('fecha_h', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tecnico" class="form-label">{{ __('Tecnico') }}</label>
            <input type="text" name="tecnico" class="form-control @error('tecnico') is-invalid @enderror" value="{{ old('tecnico', $registroV?->tecnico) }}" id="tecnico" placeholder="Tecnico">
            {!! $errors->first('tecnico', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="trabajo" class="form-label">{{ __('Trabajo') }}</label>
            <input type="text" name="trabajo" class="form-control @error('trabajo') is-invalid @enderror" value="{{ old('trabajo', $registroV?->trabajo) }}" id="trabajo" placeholder="Trabajo">
            {!! $errors->first('trabajo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cliente" class="form-label">{{ __('Cliente') }}</label>
            <input type="text" name="cliente" class="form-control @error('cliente') is-invalid @enderror" value="{{ old('cliente', $registroV?->cliente) }}" id="cliente" placeholder="Cliente">
            {!! $errors->first('cliente', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telefono" class="form-label">{{ __('Telefono') }}</label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $registroV?->telefono) }}" id="telefono" placeholder="Telefono">
            {!! $errors->first('telefono', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="valor_v" class="form-label">{{ __('Valor V') }}</label>
            <input type="text" name="valor_v" class="form-control @error('valor_v') is-invalid @enderror" value="{{ old('valor_v', $registroV?->valor_v) }}" id="valor_v" placeholder="Valor V">
            {!! $errors->first('valor_v', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
            <input type="text" name="estatus" class="form-control @error('estatus') is-invalid @enderror" value="{{ old('estatus', $registroV?->estatus) }}" id="estatus" placeholder="Estatus">
            {!! $errors->first('estatus', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="metodo_p" class="form-label">{{ __('Metodo P') }}</label>
            <input type="text" name="metodo_p" class="form-control @error('metodo_p') is-invalid @enderror" value="{{ old('metodo_p', $registroV?->metodo_p) }}" id="metodo_p" placeholder="Metodo P">
            {!! $errors->first('metodo_p', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="titular_c" class="form-label">{{ __('Titular C') }}</label>
            <input type="text" name="titular_c" class="form-control @error('titular_c') is-invalid @enderror" value="{{ old('titular_c', $registroV?->titular_c) }}" id="titular_c" placeholder="Titular C">
            {!! $errors->first('titular_c', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="cobro" class="form-label">{{ __('Cobro') }}</label>
            <input type="text" name="cobro" class="form-control @error('cobro') is-invalid @enderror" value="{{ old('cobro', $registroV?->cobro) }}" id="cobro" placeholder="Cobro">
            {!! $errors->first('cobro', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion_ce" class="form-label">{{ __('Descripcion Ce') }}</label>
            <input type="text" name="descripcion_ce" class="form-control @error('descripcion_ce') is-invalid @enderror" value="{{ old('descripcion_ce', $registroV?->descripcion_ce) }}" id="descripcion_ce" placeholder="Descripcion Ce">
            {!! $errors->first('descripcion_ce', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="monto_ce" class="form-label">{{ __('Monto Ce') }}</label>
            <input type="text" name="monto_ce" class="form-control @error('monto_ce') is-invalid @enderror" value="{{ old('monto_ce', $registroV?->monto_ce) }}" id="monto_ce" placeholder="Monto Ce">
            {!! $errors->first('monto_ce', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="metodo_pce" class="form-label">{{ __('Metodo Pce') }}</label>
            <input type="text" name="metodo_pce" class="form-control @error('metodo_pce') is-invalid @enderror" value="{{ old('metodo_pce', $registroV?->metodo_pce) }}" id="metodo_pce" placeholder="Metodo Pce">
            {!! $errors->first('metodo_pce', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="porcentaje_c" class="form-label">{{ __('Porcentaje C') }}</label>
            <input type="text" name="porcentaje_c" class="form-control @error('porcentaje_c') is-invalid @enderror" value="{{ old('porcentaje_c', $registroV?->porcentaje_c) }}" id="porcentaje_c" placeholder="Porcentaje C">
            {!! $errors->first('porcentaje_c', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="marca" class="form-label">{{ __('Marca') }}</label>
            <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $registroV?->marca) }}" id="marca" placeholder="Marca">
            {!! $errors->first('marca', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="modelo" class="form-label">{{ __('Modelo') }}</label>
            <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $registroV?->modelo) }}" id="modelo" placeholder="Modelo">
            {!! $errors->first('modelo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="año" class="form-label">{{ __('Año') }}</label>
            <input type="text" name="año" class="form-control @error('año') is-invalid @enderror" value="{{ old('año', $registroV?->año) }}" id="año" placeholder="Año">
            {!! $errors->first('año', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="col-md-3">
            <div class="form-group mb-2 mb20">
                <label for="almacen" class="form-label">{{ __('Almacén') }}</label>
                <select name="almacen" id="almacen" class="form-control">
                    <option value="">{{ __('Select Almacén') }}</option>
                    @foreach($almacenes as $almacen)
                    <option value="{{ $almacen->id_almacen }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    <div class="form-group mb-2 mb20">
        <label for="items" class="form-label">{{ __('Items') }}</label>
        <div id="items-container">
            @if (isset($registroV->items) && count($registroV->items) > 0)
            @foreach ($registroV->items as $index => $item)
            <div class="row mb-2">
                <div class="col-md-6">
                    <select name="items[{{ $index }}][producto]" class="form-control select2" {{ !$registroV->almacen ? 'disabled' : '' }}>
                        <option value="">{{ __('Select Producto') }}</option>
                        @foreach($inventario as $producto)
                        <option value="{{ $producto->id_producto }}" {{ $item['producto'] == $producto->id_producto ? 'selected' : '' }}>
                            {{ $producto->id_producto }} - {{ $item['nombre_producto'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[{{ $index }}][cantidad]" class="form-control" placeholder="Cantidad" min="1" value="{{ $item['cantidad'] }}">
                </div>
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
                    <select name="items[0][producto]" class="form-control select2" disabled>
                        <option value="">{{ __('Select Producto') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[0][cantidad]" class="form-control" placeholder="Cantidad" min="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-add-item">+</button>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>
<div class="col-md-12 mt20 mt-2">
    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
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
        let itemIndex = 1;

        function actualizarEstadoProductos() {
            const idAlmacen = $('#almacen').val();
            const productosHabilitados = idAlmacen !== "";

            $('select[name^="items"]').prop('disabled', !productosHabilitados);
        }

        function cargarProductosEnSelect($select) {
            const idAlmacen = $('#almacen').val();

            if (idAlmacen) {
                $.ajax({
                    url: '/obtener-productos-registroV',
                    type: 'GET',
                    data: {
                        id_almacen: idAlmacen
                    },
                    success: function(response) {

                        if ($select.find('option').length <= 1) {
                            $select.empty().append('<option value="">Select Producto</option>');
                            response.forEach(function(producto) {
                                $select.append(
                                    `<option value="${producto.id_producto}">${producto.id_producto} - ${producto.item}</option>` //(Cantidad: ${producto.cantidad})
                                );
                            });
                        }
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
                        <select name="items[${itemIndex}][producto]" class="form-control select2-producto" disabled>
                            <option value="">{{ __('Select Producto') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="items[${itemIndex}][cantidad]" class="form-control" placeholder="Cantidad" min="1">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-remove-item">-</button>
                    </div>
                </div>
            `;
            $('#items-container').append(newItem);

            const $newSelect = $('select[name="items[' + itemIndex + '][producto]"]').select2();

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
    });
</script>