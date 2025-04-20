<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_abonos" class="form-label">{{ __('ID de Abono') }}</label>
                    <input type="text" name="id_abonos" class="form-control @error('id_abonos') is-invalid @enderror" value="{{ old('id_abonos', $abono?->id_abonos) }}" id="id_abonos" placeholder="ID de Abono">
                    {!! $errors->first('id_abonos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_empleado" class="form-label">{{ __('Empleado') }}</label>
                    <select name="id_empleado"
                            class="form-control select2 @error('id_empleado') is-invalid @enderror" id="id_empleado"
                            style="height: 38px !important;">
                        <option value="">{{ __('Seleccione un empleado') }}</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{ $empleado->id_empleado }}" {{ old('id_empleado', $abono?->id_empleado) == $empleado->id_empleado ? 'selected' : '' }}>
                                {{ $empleado->nombre }} - {{ $empleado->cedula }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_empleado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="concepto" class="form-label">{{ __('Concepto') }}</label>
                    <input type="text" name="concepto" class="form-control @error('concepto') is-invalid @enderror" value="{{ old('concepto', $abono?->concepto) }}" id="concepto" placeholder="Concepto">
                    {!! $errors->first('concepto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="valor" class="form-label">{{ __('Valor') }}</label>
                    <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $abono?->valor) }}" id="valor" placeholder="Valor">
                    {!! $errors->first('valor', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="a_fecha" class="form-label">{{ __('Fecha') }}</label>
                    <input type="date" name="a_fecha" class="form-control @error('a_fecha') is-invalid @enderror" value="{{ old('a_fecha', $abono?->a_fecha) }}" id="a_fecha" placeholder="Fecha">
                    {!! $errors->first('a_fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
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

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
</style>

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if (typeof $().select2 === 'function') {
                $('.select2').select2();
            }
        });
    </script>
@stop

