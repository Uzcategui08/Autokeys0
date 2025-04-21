<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_tnomina" class="form-label">{{ __('ID de Nómina') }}</label>
                    <input type="text" name="id_tnomina" class="form-control @error('id_tnomina') is-invalid @enderror" value="{{ old('id_tnomina', $tnomina?->id_tnomina) }}" id="id_tnomina" placeholder="ID de Nómina">
                    {!! $errors->first('id_tnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $tnomina?->nombre) }}" id="nombre" placeholder="Nombre">
                    {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="frecuencia" class="form-label">{{ __('Frecuencia') }}</label>
                    <select name="frecuencia" id="frecuencia" class="form-control @error('frecuencia') is-invalid @enderror">
                        <option value="">{{ __('Seleccione una frecuencia') }}</option>
                        <option value="1" {{ old('frecuencia', $tnomina?->frecuencia) == 1 ? 'selected' : '' }}>{{ __('Quincenal') }}</option>
                        <option value="2" {{ old('frecuencia', $tnomina?->frecuencia) == 2 ? 'selected' : '' }}>{{ __('Mensual') }}</option>
                        <option value="3" {{ old('frecuencia', $tnomina?->frecuencia) == 3 ? 'selected' : '' }}>{{ __('Semanal') }}</option>
                    </select>
                    {!! $errors->first('frecuencia', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12 mt20 mt-2">
                <button type="submit" class="btn btn-secondary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
