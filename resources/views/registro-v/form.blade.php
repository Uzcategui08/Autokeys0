<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <!-- Sección 1: Información del Trabajo y Vehículo -->
        <div class="row mb-4">
            <!-- Columna Izquierda - Información del Trabajo -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información del Trabajo</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="fecha_h" class="form-label">{{ __('Fecha De Ejecución') }}</label>
                            <input type="date" name="fecha_h" class="form-control @error('fecha_h') is-invalid @enderror"
                                   value="{{ old('fecha_h', isset($registroV->fecha_h) ? $registroV->fecha_h->format('Y-m-d') : '') }}"  
                                   id="fecha_h" placeholder="Fecha H">
                            {!! $errors->first('fecha_h', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>
                        <div class="form-group mb-3">
                            <label for="select_empleado" class="form-label">{{ __('Técnico') }}</label>
                            <input 
                                type="hidden" 
                                name="id_empleado" 
                                id="id_empleado"
                            >
                            <input 
                                type="hidden" 
                                name="tecnico" 
                                id="tecnico" 
                                value="{{ old('tecnico', $registroV?->tecnico ?? '') }}"
                            >
                            <select 
                                id="select_empleado"
                                class="form-control @error('tecnico') is-invalid @enderror"
                                onchange="
                                    const empleadoId = this.value;
                                    const empleadoNombre = this.options[this.selectedIndex].text;
                                    
                                    document.getElementById('id_empleado').value = empleadoId;
                                    document.getElementById('tecnico').value = empleadoNombre;
                                "
                            >
                                <option value="">Seleccionar...</option>
                                @foreach($empleados as $empleado)
                                    <option 
                                        value="{{ $empleado->id_empleado }}"
                                        @selected(
                                            old('tecnico', $registroV?->tecnico) == $empleado->nombre ||
                                            old('id_empleado') == $empleado->id_empleado
                                        )
                                    >
                                        {{ $empleado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @error('tecnico')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="lugarventa" class="form-label">{{ __('Lugar de Venta') }}</label>
                            <select name="lugarventa" class="form-control select2 @error('lugarventa') is-invalid @enderror" id="lugarventa">
                                <option value="">{{ __('Seleccione el Lugar de Venta') }}</option>
                                <option value="Local" {{ old('lugarventa', $registroV?->lugarventa) == 'Local' ? 'selected' : '' }}>Local</option>
                                <option value="Van Grande" {{ old('lugarventa', $registroV?->lugarventa) == 'Van Grande' ? 'selected' : '' }}>Van Grande</option>
                                <option value="Van Grande-Pulga" {{ old('lugarventa', $registroV?->lugarventa) == 'Van Grande-Pulga' ? 'selected' : '' }}>Van Grande-Pulga</option>
                                <option value="Van Pequeña" {{ old('lugarventa', $registroV?->lugarventa) == 'Van Pequeña' ? 'selected' : '' }}>Van Pequeña</option>
                                <option value="Van Pequeña-Pulga" {{ old('lugarventa', $registroV?->lugarventa) == 'Van Pequeña-Pulga' ? 'selected' : '' }}>Van Pequeña-Pulga</option>

                            </select>
                            {!! $errors->first('trabajo', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>

                        <div class="form-group mb-3">
                            <label for="trabajo" class="form-label">{{ __('Trabajo') }}</label>
                            <select name="trabajo" class="form-control select2 @error('trabajo') is-invalid @enderror" id="trabajo">
                                <option value="">{{ __('Seleccione un tipo de trabajo') }}</option>
                                <option value="duplicado" {{ old('trabajo', $registroV?->trabajo) == 'duplicado' ? 'selected' : '' }}>Duplicado</option>
                                <option value="perdida" {{ old('trabajo', $registroV?->trabajo) == 'perdida' ? 'selected' : '' }}>Pérdida</option>
                                <option value="programacion" {{ old('trabajo', $registroV?->trabajo) == 'programacion' ? 'selected' : '' }}>Programación</option>
                                <option value="alarma" {{ old('trabajo', $registroV?->trabajo) == 'alarma' ? 'selected' : '' }}>Alarma</option>
                                <option value="airbag" {{ old('trabajo', $registroV?->trabajo) == 'airbag' ? 'selected' : '' }}>Airbag</option>
                                <option value="rekey" {{ old('trabajo', $registroV?->trabajo) == 'rekey' ? 'selected' : '' }}>Rekey</option>
                                <option value="lishi" {{ old('trabajo', $registroV?->trabajo) == 'lishi' ? 'selected' : '' }}>Lishi</option>
                                <option value="remote start" {{ old('trabajo', $registroV?->trabajo) == 'remote_start' ? 'selected' : '' }}>Remote Start</option>
                                <option value="control" {{ old('trabajo', $registroV?->trabajo) == 'control' ? 'selected' : '' }}>Control</option>
                                <option value="venta" {{ old('trabajo', $registroV?->trabajo) == 'venta' ? 'selected' : '' }}>Venta</option>
                                <option value="apertura" {{ old('trabajo', $registroV?->trabajo) == 'apertura' ? 'selected' : '' }}>Apertura</option>
                                <option value="cambio_chip" {{ old('trabajo', $registroV?->trabajo) == 'cambio_chip' ? 'selected' : '' }}>Cambio de Chip</option>
                                <option value="revision" {{ old('trabajo', $registroV?->trabajo) == 'revision' ? 'selected' : '' }}>Revisión</option>
                                <option value="suiche" {{ old('trabajo', $registroV?->trabajo) == 'suiche' ? 'selected' : '' }}>Suiche</option>
                                <option value="llave puerta" {{ old('trabajo', $registroV?->trabajo) == 'llave_puerta' ? 'selected' : '' }}>Hacer llave de Puerta</option>
                                <option value="cinturon" {{ old('trabajo', $registroV?->trabajo) == 'cinturon' ? 'selected' : '' }}>Cinturón</option>
                                <option value="diag" {{ old('trabajo', $registroV?->trabajo) == 'diag' ? 'selected' : '' }}>Diag</option>
                                <option value="emuladores" {{ old('trabajo', $registroV?->trabajo) == 'emuladores' ? 'selected' : '' }}>Emuladores</option>
                                <option value="clonacion" {{ old('trabajo', $registroV?->trabajo) == 'clonacion' ? 'selected' : '' }}>Clonación</option>
                            </select>
                            {!! $errors->first('trabajo', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Información del Vehículo -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información del Vehículo</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="marca" class="form-label">{{ __('Marca') }}</label>
                            <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                                value="{{ old('marca', $registroV?->marca) }}" id="marca" placeholder="Marca">
                            {!! $errors->first('marca', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>

                        <div class="form-group mb-3">
                            <label for="modelo" class="form-label">{{ __('Modelo') }}</label>
                            <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                                value="{{ old('modelo', $registroV?->modelo) }}" id="modelo" placeholder="Modelo">
                            {!! $errors->first('modelo', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>

                        <div class="form-group mb-3">
                            <label for="año" class="form-label">{{ __('Año') }}</label>
                            <input type="text" name="año" class="form-control @error('año') is-invalid @enderror" 
                                value="{{ old('año', $registroV?->año) }}" id="año" placeholder="Año">
                            {!! $errors->first('año', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Items de Trabajo -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Items de Trabajo</h5>
                        <button type="button" class="btn btn-success btn-sm btn-add-work">
                            <i class="fas fa-plus-circle me-1"></i> Agregar Trabajo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group" id="items-container">
                            <!-- Los items dinámicos se insertarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Información del Cliente -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="id_cliente" class="form-label">{{ __('Cliente') }}</label>
                                    <select name="cliente" id="id_cliente" class="form-control select2 @error('id_cliente') is-invalid @enderror">
                                        <option value="">{{ __('Seleccione un cliente') }}</option>
                                        @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->nombre }}"
                                            data-telefono="{{ $cliente->telefono }}"
                                            {{ old('cliente', $registroV?->cliente) == $cliente->nombre ? 'selected' : '' }}>
                                            {{ $cliente->nombre }} {{ $cliente->apellido ?? '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('cliente', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>

                                <div class="col-md-4">
                                    <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
                                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                        id="telefono" placeholder="Teléfono" readonly>
                                    {!! $errors->first('telefono', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 4: Costos Extras -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Costos Extras</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="descripcion_ce" class="form-label">{{ __('Descripción') }}</label>
                                    <input type="text" name="descripcion_ce" class="form-control @error('descripcion_ce') is-invalid @enderror" 
                                        value="{{ old('descripcion_ce', $registroV?->descripcion_ce) }}" id="descripcion_ce" placeholder="Descripción">
                                    {!! $errors->first('descripcion_ce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            
                            <div class="col-md-1">
                                <div class="form-group mb-3">
                                    <label for="monto_ce" class="form-label">{{ __('Monto') }}</label>
                                    <input type="text" name="monto_ce" class="form-control @error('monto_ce') is-invalid @enderror" 
                                        value="{{ old('monto_ce', $registroV?->monto_ce) }}" id="monto_ce" placeholder="Monto">
                                    {!! $errors->first('monto_ce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="metodo_pce" class="form-label">{{ __('Método de Pago') }}</label>
                                    <input type="text" name="metodo_pce" class="form-control @error('metodo_pce') is-invalid @enderror" 
                                        value="{{ old('metodo_pce', $registroV?->metodo_pce) }}" id="metodo_pce" placeholder="Método">
                                    {!! $errors->first('metodo_pce', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="cobro" class="form-label">{{ __('Cobro') }}</label>
                                    <input type="text" name="cobro" class="form-control @error('cobro') is-invalid @enderror" 
                                        value="{{ old('cobro', $registroV?->cobro) }}" id="cobro" placeholder="Cobro">
                                    {!! $errors->first('cobro', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="porcentaje_c" class="form-label">{{ __('% Cerrajero') }}</label>
                                    <input type="text" name="porcentaje_c" class="form-control @error('porcentaje_c') is-invalid @enderror" 
                                        value="{{ old('porcentaje_c', $registroV?->porcentaje_c) }}" id="porcentaje_c" placeholder="Porcentaje" readonly>
                                    {!! $errors->first('porcentaje_c', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 5: Información de Pago -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="valor_v" class="form-label">{{ __('Valor') }}</label>
                                    <input type="text" name="valor_v" class="form-control @error('valor_v') is-invalid @enderror" 
                                        value="{{ old('valor_v', $registroV?->valor_v) }}" id="valor_v" placeholder="Valor">
                                    {!! $errors->first('valor_v', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="titular_c" class="form-label">{{ __('Titular') }}</label>
                                    <input type="text" name="titular_c" class="form-control @error('titular_c') is-invalid @enderror" 
                                        value="{{ old('titular_c', $registroV?->titular_c) }}" id="titular_c" placeholder="Titular">
                                    {!! $errors->first('titular_c', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                                                        
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
                                    <select name="estatus" id="estatus" class="form-control @error('estatus') is-invalid @enderror" readonly>
                                        <option value="">Estado</option>
                                        <option value="pagado" {{ old('estatus', $registroV?->estatus) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                        <option value="pendiente" {{ old('estatus', $registroV?->estatus) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="parcialementep" {{ old('estatus', $registroV?->estatus) == 'parcialementep' ? 'selected' : '' }}>Parcial</option>
                                    </select>
                                    {!! $errors->first('estatus', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 6: Registro de Pagos Parciales -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Registro de Pagos Parciales</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Valor Total:</strong> 
                                    <span id="valor-total">${{ number_format($registroV->valor_v ?? 0, 2) }}</span>
                                </div>
                                <div>
                                    <strong>Total Pagado:</strong> 
                                    <span id="total-pagado">$0.00</span>
                                </div>
                                <div>
                                    <strong>Saldo Pendiente:</strong> 
                                    <span id="saldo-pendiente">${{ number_format($registroV->valor_v ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" id="pago_monto" class="form-control" placeholder="0.00">
                                <small class="text-muted">Monto máximo: <span id="maximo-pago">${{ number_format($registroV->valor_v ?? 0, 2) }}</span></small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Método de Pago</label>
                                <select id="pago_metodo" class="form-control">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" id="pago_fecha" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <button type="button" id="btn-agregar-pago" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Agregar Pago
                        </button>

                        <div class="mt-4" id="lista-pagos">
                            @if(!empty($registroV->pagos) && is_array($registroV->pagos))
                                @foreach($registroV->pagos as $index => $pago)
                                <div class="pago-item card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold">${{ number_format($pago['monto'], 2) }}</span>
                                                <span class="text-muted ms-2">({{ $pago['metodo_pago'] }})</span>
                                                <small class="text-muted ms-2">{{ $pago['fecha'] }}</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="{{ $index }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="alert alert-warning">No hay pagos registrados</div>
                            @endif
                        </div>
                    </div>
                </div>

                <input type="hidden" name="pagos" id="pagos_json" value='@json(old('pagos', $registroV->pagos ?? []))'>
            </div>
        </div>

        <!-- Sección 7: Botón de Envío -->
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i> {{ __('Grabar Registro') }}
                </button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        dropdownAutoWidth: true,
    });
    
});
</script>
<style>
.select2-container .select2-selection--single {
    height: 38px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
.pago-item {
transition: all 0.3s ease;
}
.pago-item:hover {
    background-color: #f8f9fa;
}
.btn-eliminar-pago {
    transition: all 0.2s ease;
}
.btn-eliminar-pago:hover {
    transform: scale(1.1);
}
#valor-total, #total-pagado, #saldo-pendiente {
    font-weight: bold;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let valorTotal = parseFloat($('#valor_v').val()) || 0;
    let totalPagado = 0;
    let saldoPendiente = valorTotal;

    actualizarResumen();

    $('#valor_v').on('change', function() {
        valorTotal = parseFloat($(this).val()) || 0;
        actualizarResumen();
        actualizarMaximoPago();
    });

    $('#estatus').on('change', function() {

        if(totalPagado === 0) {
            if($(this).val() === 'pagado') {

                agregarPagoCompleto();
            }
        }
    });

    function agregarPagoCompleto() {
        const pagosJson = JSON.stringify([{
            monto: valorTotal,
            metodo_pago: 'completo',
            fecha: new Date().toISOString().split('T')[0]
        }]);
        
        $('#pagos_json').val(pagosJson);
        actualizarListaPagos();
        actualizarResumen();
    }

    function actualizarResumen() {
        totalPagado = calcularTotalPagado();
        saldoPendiente = valorTotal - totalPagado;
        
        $('#valor-total').text('$' + valorTotal.toFixed(2));
        $('#total-pagado').text('$' + totalPagado.toFixed(2));
        $('#saldo-pendiente').text('$' + (saldoPendiente > 0 ? saldoPendiente.toFixed(2) : '0.00'));

        actualizarEstatus();
    }

    function actualizarMaximoPago() {
        const maxPago = saldoPendiente > 0 ? saldoPendiente : 0;
        $('#maximo-pago').text('$' + maxPago.toFixed(2));
        $('#pago_monto').attr('max', maxPago);
    }

    function calcularTotalPagado() {
        const pagosJson = $('#pagos_json').val() || '[]';
        try {
            const jsonStr = pagosJson.replace(/^"|"$/g, '');
            const pagos = JSON.parse(jsonStr);
            return pagos.reduce((total, pago) => total + parseFloat(pago.monto), 0);
        } catch (e) {
            return 0;
        }
    }

    function actualizarEstatus() {
        if (saldoPendiente <= 0.01) { 
            $('#estatus').val('pagado');
        } else if (totalPagado > 0) {
            $('#estatus').val('parcialementep'); 
        } else {
            $('#estatus').val('pendiente');
        }
    }

    $('#btn-agregar-pago').click(function() {
        const monto = parseFloat($('#pago_monto').val());
        const metodo = $('#pago_metodo').val();
        const fecha = $('#pago_fecha').val();
        
        if (!monto || monto <= 0) {
            alert('Error: Ingrese un monto válido mayor a cero');
            return;
        }
        
        if (monto > saldoPendiente) {
            alert('Error: El monto excede el saldo pendiente de $' + saldoPendiente.toFixed(2));
            return;
        }
        
        const pagosJson = $('#pagos_json').val() || '[]';
        let pagos = [];
        
        try {
            pagos = JSON.parse(pagosJson);
            if (!Array.isArray(pagos)) pagos = [];
        } catch (e) {
            console.error('Error parseando pagos:', e);
        }
        
        pagos.push({
            monto: monto,
            metodo_pago: metodo,
            fecha: fecha
        });
        
        $('#pagos_json').val(JSON.stringify(pagos));
        
        actualizarListaPagos();
        actualizarResumen();
        actualizarMaximoPago();
        
        $('#pago_monto').val('').focus();
    });

    function actualizarListaPagos() {
        const pagosJson = $('#pagos_json').val() || '[]';
        $('#lista-pagos').empty();
        
        try {
            const pagos = JSON.parse(pagosJson);
            
            if (pagos.length === 0) {
                $('#lista-pagos').html('<div class="alert alert-warning">No hay pagos registrados</div>');
                return;
            }
            
            pagos.forEach((pago, index) => {
                $('#lista-pagos').append(`
                    <div class="pago-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">$${parseFloat(pago.monto).toFixed(2)}</span>
                                    <span class="text-muted ms-2">(${pago.metodo_pago})</span>
                                    <small class="text-muted ms-2">${pago.fecha}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pago" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
        } catch (e) {
            console.error('Error mostrando pagos:', e);
            $('#lista-pagos').html('<div class="alert alert-danger">Error mostrando los pagos</div>');
        }
    }

    $(document).on('click', '.btn-eliminar-pago', function() {
        const index = $(this).data('index');
        const pagosJson = $('#pagos_json').val() || '[]';
        
        try {
            let pagos = JSON.parse(pagosJson);
            
            if (index >= 0 && index < pagos.length) {
                pagos.splice(index, 1);
                
                $('#pagos_json').val(JSON.stringify(pagos));
                actualizarListaPagos();
                actualizarResumen();
                actualizarMaximoPago();
            }
        } catch (e) {
            console.error('Error eliminando pago:', e);
        }
    });

    actualizarListaPagos();
    actualizarMaximoPago();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('#id_cliente').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un cliente',
        allowClear: true
    });

    const telefonoInput = document.getElementById('telefono');

    // Escuchar cambios usando jQuery (compatible con Select2)
    $('#id_cliente').on('change', function() {
        const selectedOption = $(this).find(':selected');
        telefonoInput.value = selectedOption.data('telefono') || '';
    });

    // Actualizar al cargar si hay valor inicial
    if ($('#id_cliente').val()) {
        const initialOption = $('#id_cliente').find(':selected');
        telefonoInput.value = initialOption.data('telefono') || '';
    }
});
</script>
<script>document.addEventListener('DOMContentLoaded', function() {
    const valorVentaInput = document.getElementById('valor_v');
    const montoCEInput = document.getElementById('monto_ce');
    const porcentajeCInput = document.getElementById('porcentaje_c');

    function calcularPorcentaje() {
        const valorVenta = parseFloat(valorVentaInput.value) || 0;
        const montoCE = parseFloat(montoCEInput.value) || 0;
        
        // Aplicar la fórmula: (Valor venta - Monto costo extra) * 0.36
        const porcentaje = (valorVenta - montoCE) * 0.36;
        
        // Actualizar el campo solo si el cálculo es válido
        if (!isNaN(porcentaje)) {
            porcentajeCInput.value = porcentaje.toFixed(2); // 2 decimales
        } else {
            porcentajeCInput.value = '';
        }
    }

    // Escuchar cambios en ambos campos
    valorVentaInput.addEventListener('input', calcularPorcentaje);
    montoCEInput.addEventListener('input', calcularPorcentaje);
});</script>
<script>
$(document).ready(function() {
    let itemGroupIndex = 0;
    const itemsExistentes = @json($registroV->items ?? []);

    function cargarProductosEnSelect($select, idAlmacen, productoSeleccionado = null, nombreProducto = null) {
        if (idAlmacen) {
            $.ajax({
                url: '/obtener-productos-orden',
                type: 'GET',
                data: { id_almacen: idAlmacen },
                success: function(response) {

                    let options = '<option value="">{{ __('Select Producto') }}</option>';
                    
                    if (productoSeleccionado) {
                        const productoEncontrado = response.find(p => p.id_producto == productoSeleccionado);
                        
                        if (productoEncontrado) {
                            options += `
                                <option value="${productoEncontrado.id_producto}" selected>
                                    ${productoEncontrado.id_producto} - ${productoEncontrado.item}
                                </option>`;
                        } else if (nombreProducto) {
                            options += `
                                <option value="${productoSeleccionado}" selected>
                                    ${productoSeleccionado} - ${nombreProducto}
                                </option>`;
                        }
                    }
                    
                    response.forEach(function(producto) {
                        if (producto.id_producto != productoSeleccionado) {
                            options += `
                                <option value="${producto.id_producto}">
                                    ${producto.id_producto} - ${producto.item}
                                </option>`;
                        }
                    });
                    
                    $select.html(options).prop('disabled', false);

                    $select.select2();
                    if (productoSeleccionado) {
                        $select.val(productoSeleccionado).trigger('change');
                    }

                },
                error: function(xhr) {
                    console.error('Error al cargar los productos');
                    if (productoSeleccionado && nombreProducto) {
                        $select.html(`
                            <option value="${productoSeleccionado}" selected>
                                ${productoSeleccionado} - ${nombreProducto}
                            </option>
                            <option value="">{{ __('Select Producto') }}</option>
                        `).prop('disabled', false);
                        $select.select2();
                    }
                }
            });
        } else {
            if (productoSeleccionado && nombreProducto) {
                $select.html(`
                    <option value="${productoSeleccionado}" selected>
                        ${productoSeleccionado} - ${nombreProducto}
                    </option>
                    <option value="">{{ __('Select Producto') }}</option>
                `).prop('disabled', false);
                $select.select2();
            } else {
                $select.html('<option value="">{{ __('Select Producto') }}</option>').prop('disabled', true);
            }
        }
    }


   function addNewProductRow(itemGroup, productoData = null) {
        const productoIndex = itemGroup.find('.producto-row').length;
        const itemGroupIndex = itemGroup.data('index');

        const newProductoRow = `
            <div class="row mb-2 producto-row">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Producto') }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][producto]"
                            class="form-control select2-producto" ${productoData ? '' : 'disabled'}>
                        <option value="">{{ __('Select Producto') }}</option>
                        ${productoData ? `
                            <option value="${productoData.producto}" selected>
                                ${productoData.producto} - ${productoData.nombre_producto || 'Producto'}
                            </option>
                        ` : ''}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Cantidad') }}</label>
                    <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][cantidad]"
                           class="form-control" placeholder="Cantidad" min="1"
                           value="${productoData ? productoData.cantidad : ''}">
                </div>
                <div class="col-md-2">
                <label class="form-label">{{ __('Precio') }}</label>
                <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][precio]"
                        class="form-control" placeholder="Precio" min="1"
                        value="${productoData ? productoData.precio : ''}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Almacén') }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][almacen]"
                            class="form-control select-almacen">
                        <option value="">{{ __('Select Almacén') }}</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id_almacen }}"
                                ${productoData && productoData.almacen == '{{ $almacen->id_almacen }}' ? 'selected' : ''}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-remove-producto mt-4"><i class="fa fa-minus-circle" aria-hidden="true"></i></button>
                </div>
            </div>
        `;

        itemGroup.find('.productos-container').append(newProductoRow);
        const $newRow = itemGroup.find('.producto-row').last();

        if (productoData) {
            const $selectAlmacen = $newRow.find('.select-almacen');
            const $selectProducto = $newRow.find('.select2-producto');

            cargarProductosEnSelect(
                $selectProducto,
                productoData.almacen,
                productoData.producto,
                productoData.nombre_producto,
                productoData.cantidad
            );
        }
    }

    function addNewItemGroup(itemData = null) {
        const currentIndex = itemGroupIndex;
        const newItemGroup = $(`
            <div class="item-group mb-4 p-3 border rounded" data-index="${currentIndex}">
                <div class="row mb-2">
                    <div class="col-md-11">
                        <label class="form-label">{{ __('Trabajo y Productos') }}</label>
                        <textarea name="items[${currentIndex}][trabajo]" class="form-control" placeholder="Descripción del trabajo">${itemData ? (itemData.trabajo || '') : ''}</textarea>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-item-group mt-4"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="productos-container"></div>
                <button type="button" class="btn btn-success btn-add-producto mt-2">{{ __('Agregar Producto') }}</button>
            </div>
        `);

        $('#items-container').append(newItemGroup);

        if (itemData && itemData.productos) {
            itemData.productos.forEach(producto => {
                addNewProductRow(newItemGroup, producto);
            });
        }
        
        itemGroupIndex++;
    }

    $(document).on('click', '.btn-add-work', function() {
        addNewItemGroup();
    });

    $(document).on('click', '.btn-add-producto', function() {
        const itemGroup = $(this).closest('.item-group');
        addNewProductRow(itemGroup);
    });

    $(document).on('click', '.btn-remove-producto', function() {
        $(this).closest('.producto-row').remove();
    });

    $(document).on('click', '.btn-remove-item-group', function() {
        $(this).closest('.item-group').remove();
    });

    $(document).on('change', 'select[name^="items"][name$="[almacen]"]', function() {
        const $row = $(this).closest('.producto-row');
        const $selectProducto = $row.find('.select2-producto');
        const productoSeleccionado = $selectProducto.val();
        const nombreProducto = $selectProducto.find('option:selected').text().split('-')[1]?.trim();
        
        cargarProductosEnSelect(
            $selectProducto, 
            $(this).val(), 
            productoSeleccionado,
            nombreProducto
        );
    });

    if (itemsExistentes && itemsExistentes.length > 0) {
        itemsExistentes.forEach(item => {
            addNewItemGroup(item);
        });
        
        $('.item-group').each(function() {
            const itemGroupIndex = $(this).data('index');
            const itemData = itemsExistentes[itemGroupIndex];
            
            if (itemData && itemData.productos) {
                $(this).find('.producto-row').each(function(index) {
                    const productoData = itemData.productos[index];
                    if (productoData) {
                        const $selectProducto = $(this).find('.select2-producto');
                        const $selectAlmacen = $(this).find('.select-almacen');
                        
                        cargarProductosEnSelect(
                            $selectProducto,
                            productoData.almacen,
                            productoData.producto,
                            productoData.nombre_producto,
                            productoData.cantidad
                        );
                    }
                });
            }
        });
    } else {
        addNewItemGroup();
    }
});
</script>