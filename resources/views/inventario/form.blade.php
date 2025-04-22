<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="id_producto" class="form-label">{{ __('Producto') }}</label>
            <select name="id_producto" class="form-control @error('id_producto') is-invalid @enderror" id="id_producto">
                <option value="">Seleccione un producto</option>
                @foreach ($productos as $producto)
                <option value="{{ $producto->id_producto }}" {{ old('id_producto', $inventario?->id_producto) == $producto->id_producto ? 'selected' : '' }}>
                    {{ $producto->item }} (ID: {{ $producto->id_producto }})
                </option>
                @endforeach
            </select>
            {!! $errors->first('id_producto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_almacen" class="form-label">{{ __('Almacén') }}</label>
            <select name="id_almacen" class="form-control @error('id_almacen') is-invalid @enderror" id="id_almacen">
                <option value="">Seleccione un almacén</option>
                @foreach ($almacenes as $almacen)
                <option value="{{ $almacen->id_almacen }}" {{ old('id_almacen', $inventario?->id_almacen) == $almacen->id_almacen ? 'selected' : '' }}>
                    {{ $almacen->nombre }} (ID: {{ $almacen->id_almacen }})
                </option>
                @endforeach
            </select>
            {!! $errors->first('id_almacen', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="cantidad" class="form-label">{{ __('Cantidad') }}</label>
            <input type="text" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', $inventario?->cantidad) }}" id="cantidad" placeholder="Cantidad">
            {!! $errors->first('cantidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>