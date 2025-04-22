<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_empleado" class="form-label">{{ __('ID del Empleado') }}</label>
                    <input type="text" name="id_empleado" class="form-control @error('id_empleado') is-invalid @enderror" value="{{ old('id_empleado', $empleado?->id_empleado) }}" id="id_empleado" placeholder="ID del Empleado">
                    {!! $errors->first('id_empleado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $empleado?->nombre) }}" id="nombre" placeholder="Nombre">
                    {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="cedula" class="form-label">{{ __('Cédula') }}</label>
                    <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula', $empleado?->cedula) }}" id="cedula" placeholder="Cédula">
                    {!! $errors->first('cedula', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="cargo" class="form-label">{{ __('Cargo') }}</label>
                    <input type="text" name="cargo" class="form-control @error('cargo') is-invalid @enderror" value="{{ old('cargo', $empleado?->cargo) }}" id="cargo" placeholder="Cargo">
                    {!! $errors->first('cargo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="salario_base" class="form-label">{{ __('Salario Base') }}</label>
                    <input type="text" name="salario_base" class="form-control @error('salario_base') is-invalid @enderror" value="{{ old('salario_base', $empleado?->salario_base) }}" id="salario_base" placeholder="Salario Base">
                    {!! $errors->first('salario_base', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="metodo_pago" class="form-label">{{ __('Método de Pago') }}</label>
                    <input type="text" name="metodo_pago" class="form-control @error('metodo_pago') is-invalid @enderror" value="{{ old('metodo_pago', $empleado?->metodo_pago) }}" id="metodo_pago" placeholder="Método de Pago">
                    {!! $errors->first('metodo_pago', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="form-group mb-3">
                <label for="id_tnomina">Tipo de Nómina</label>
                <select name="id_tnomina" id="id_tnomina" class="form-control @error('id_tnomina') is-invalid @enderror">
                    <option value="">Seleccione una opción</option>
                    @foreach($tnominas as $tnomina)
                        <option value="{{ $tnomina->id_tnomina }}" {{ old('id_tnomina') == $tnomina->id_tnomina ? 'selected' : '' }}>
                            {{ $tnomina->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_tnomina')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12 mt20 mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
            </div>
        </div>
    </div>
</div>
