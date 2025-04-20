<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_prestamos" class="form-label">{{ __('ID Préstamos') }}</label>
                    <input type="text" name="id_prestamos" class="form-control @error('id_prestamos') is-invalid @enderror" value="{{ old('id_prestamos', $prestamo?->id_prestamos) }}" id="id_prestamos" placeholder="Id Prestamos">
                    {!! $errors->first('id_prestamos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_empleado" class="form-label">{{ __('ID Empleado') }}</label>
                    <input type="text" name="id_empleado" class="form-control @error('id_empleado') is-invalid @enderror" value="{{ old('id_empleado', $prestamo?->id_empleado) }}" id="id_empleado" placeholder="Id Empleado">
                    {!! $errors->first('id_empleado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="valor" class="form-label">{{ __('Valor') }}</label>
                    <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $prestamo?->valor) }}" id="valor" placeholder="Valor">
                    {!! $errors->first('valor', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="cuotas" class="form-label">{{ __('Cantidad de Cuotas para el préstamo') }}</label>
                    <input type="number" 
                        name="cuotas" 
                        class="form-control @error('cuotas') is-invalid @enderror" 
                        value="{{ old('cuotas', $prestamo->cuotas ?? 1) }}" 
                        id="cuotas" 
                        placeholder="Cuotas"
                        min="1"
                        required>
                    {!! $errors->first('cuotas', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <input type="hidden" name="cuota_actual" value="0">
            </div>
            <div class="col-md-4">
                <input type="hidden" name="activo" value="1">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt20 mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
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