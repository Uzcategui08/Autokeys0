<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="id_nempleado" class="form-label">{{ __('Id Nempleado') }}</label>
            <input type="text" name="id_nempleado" class="form-control @error('id_nempleado') is-invalid @enderror" value="{{ old('id_nempleado', $nempleado?->id_nempleado) }}" id="id_nempleado" placeholder="Id Nempleado">
            {!! $errors->first('id_nempleado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="id_pnomina" class="form-label">{{ __('Id Pnomina') }}</label>
            <input type="text" name="id_pnomina" class="form-control @error('id_pnomina') is-invalid @enderror" value="{{ old('id_pnomina', $nempleado?->id_pnomina) }}" id="id_pnomina" placeholder="Id Pnomina">
            {!! $errors->first('id_pnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="id_empleado" class="form-label">{{ __('Id Empleado') }}</label>
            <input type="text" name="id_empleado" class="form-control @error('id_empleado') is-invalid @enderror" value="{{ old('id_empleado', $nempleado?->id_empleado) }}" id="id_empleado" placeholder="Id Empleado">
            {!! $errors->first('id_empleado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_descuentos" class="form-label">{{ __('Total Descuentos') }}</label>
            <input type="text" name="total_descuentos" class="form-control @error('total_descuentos') is-invalid @enderror" value="{{ old('total_descuentos', $nempleado?->total_descuentos) }}" id="total_descuentos" placeholder="Total Descuentos">
            {!! $errors->first('total_descuentos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_abonos" class="form-label">{{ __('Total Abonos') }}</label>
            <input type="text" name="total_abonos" class="form-control @error('total_abonos') is-invalid @enderror" value="{{ old('total_abonos', $nempleado?->total_abonos) }}" id="total_abonos" placeholder="Total Abonos">
            {!! $errors->first('total_abonos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_prestamos" class="form-label">{{ __('Total Prestamos') }}</label>
            <input type="text" name="total_prestamos" class="form-control @error('total_prestamos') is-invalid @enderror" value="{{ old('total_prestamos', $nempleado?->total_prestamos) }}" id="total_prestamos" placeholder="Total Prestamos">
            {!! $errors->first('total_prestamos', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="total_pagado" class="form-label">{{ __('Total Pagado') }}</label>
            <input type="text" name="total_pagado" class="form-control @error('total_pagado') is-invalid @enderror" value="{{ old('total_pagado', $nempleado?->total_pagado) }}" id="total_pagado" placeholder="Total Pagado">
            {!! $errors->first('total_pagado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>