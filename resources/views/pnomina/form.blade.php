<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_pnomina" class="form-label">{{ __('ID Período de Nómina') }}</label>
                    <input type="text" name="id_pnomina" class="form-control @error('id_pnomina') is-invalid @enderror" value="{{ old('id_pnomina', $pnomina?->id_pnomina) }}" id="id_pnomina" placeholder="ID Período de Nómina">
                    {!! $errors->first('id_pnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_tnomina" class="form-label">{{ __('Tipo de Nómina') }}</label>
                    <select name="id_tnomina" id="id_tnomina" class="form-control @error('id_tnomina') is-invalid @enderror">
                        <option value="">{{ __('Seleccione un tipo de nómina') }}</option>
                        @foreach($tnominas as $tnomina)
                            <option value="{{ $tnomina->id_tnomina }}" {{ old('id_tnomina', $pnomina?->id_tnomina) == $tnomina->id_tnomina ? 'selected' : '' }}>
                                {{ $tnomina->nombre }} - {{ [
                                    1 => 'Quincenal',
                                    2 => 'Mensual',
                                    3 => 'Semanal',
                                ][$tnomina->frecuencia] ?? 'Frecuencia desconocida' }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_tnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>            
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="inicio" class="form-label">{{ __('Inicio') }}</label>
                    <input type="date" name="inicio" class="form-control @error('inicio') is-invalid @enderror" 
                           value="{{ old('inicio', isset($pnomina->inicio) ? $pnomina->inicio->format('Y-m-d') : '') }}" 
                           id="inicio" required>
                    {!! $errors->first('inicio', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="fin" class="form-label">{{ __('Fin') }}</label>
                    <input type="date" name="fin" class="form-control @error('fin') is-invalid @enderror" 
                           value="{{ old('fin', isset($pnomina->fin) ? $pnomina->fin->format('Y-m-d') : '') }}" 
                           id="fin" required>
                    {!! $errors->first('fin', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>          
        </div>
        <div class="row">
            <div class="col-md-12 mt20 mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
