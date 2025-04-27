<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2 mb20">
                    <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $empleado?->nombre) }}" id="nombre" placeholder="Nombre">
                    {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-6">
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
                    <select name="cargo" class="form-control @error('cargo') is-invalid @enderror" id="cargo">
                        <option value="">{{ __('Seleccionar Cargo') }}</option>
                        <option value="1" {{ old('cargo', $empleado?->cargo) == 1 ? 'selected' : '' }}>Técnico</option>
                        <option value="2" {{ old('cargo', $empleado?->cargo) == 2 ? 'selected' : '' }}>Administrativo</option>
                        <option value="3" {{ old('cargo', $empleado?->cargo) == 3 ? 'selected' : '' }}>Supervisor</option>
                        <option value="4" {{ old('cargo', $empleado?->cargo) == 4 ? 'selected' : '' }}>Gerente</option>
                    </select>
                    {!! $errors->first('cargo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="tipo" class="form-label">{{ __('Tipo') }}</label>
                    <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" id="tipo">
                        <option value="">{{ __('Seleccionar Tipo') }}</option>
                        <option value="1" {{ old('tipo', $empleado?->tipo) == 1 ? 'selected' : '' }}>Costo</option>
                        <option value="2" {{ old('tipo', $empleado?->tipo) == 2 ? 'selected' : '' }}>Gasto</option>
                    </select>
                    {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="salario_base" class="form-label">{{ __('Salario Base') }}</label>
                    <input type="text" name="salario_base" class="form-control @error('salario_base') is-invalid @enderror" value="{{ old('salario_base', $empleado?->salario_base) }}" id="salario_base" placeholder="Salario Base">
                    {!! $errors->first('salario_base', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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