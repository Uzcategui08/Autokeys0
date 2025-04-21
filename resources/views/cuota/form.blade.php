<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="id_cuotas" class="form-label">{{ __('Id Cuotas') }}</label>
            <input type="text" name="id_cuotas" class="form-control @error('id_cuotas') is-invalid @enderror" value="{{ old('id_cuotas', $cuota?->id_cuotas) }}" id="id_cuotas" placeholder="Id Cuotas">
            {!! $errors->first('id_cuotas', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="id_prestamos" class="form-label">{{ __('Id Prestamos') }}</label>
            <input type="text" name="id_prestamos" class="form-control @error('id_prestamos') is-invalid @enderror" value="{{ old('id_prestamos', $cuota?->id_prestamos) }}" id="id_prestamos" placeholder="Id Prestamos">
            {!! $errors->first('id_prestamos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="valor" class="form-label">{{ __('Valor') }}</label>
            <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $cuota?->valor) }}" id="valor" placeholder="Valor">
            {!! $errors->first('valor', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="fecha_vencimiento" class="form-label">{{ __('Fecha Vencimiento') }}</label>
            <input type="text" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror" value="{{ old('fecha_vencimiento', $cuota?->fecha_vencimiento) }}" id="fecha_vencimiento" placeholder="Fecha Vencimiento">
            {!! $errors->first('fecha_vencimiento', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="pagada" class="form-label">{{ __('Pagada') }}</label>
            <input type="text" name="pagada" class="form-control @error('pagada') is-invalid @enderror" value="{{ old('pagada', $cuota?->pagada) }}" id="pagada" placeholder="Pagada">
            {!! $errors->first('pagada', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>