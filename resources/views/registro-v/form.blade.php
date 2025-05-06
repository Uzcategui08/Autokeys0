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
    
    @if(auth()->user()->hasRole('limited_user'))
        @if(auth()->user()->empleado)
            <!-- Mostrar solo el nombre del usuario actual (solo lectura) -->
            <input type="text" class="form-control" value="{{ auth()->user()->empleado->nombre }}" readonly>
            <input type="hidden" name="id_empleado" value="{{ auth()->user()->empleado->id_empleado }}">
        @else
            <div class="alert alert-danger">
                No tienes un empleado asociado en el sistema. Contacta al administrador.
            </div>
        @endif
    @else
        <!-- Select normal para otros roles -->
        <select 
            id="select_empleado"
            name="id_empleado"  
            class="form-control @error('id_empleado') is-invalid @enderror"
        >
            <option value="">Seleccionar...</option>
            @foreach($empleados as $empleado)
                <option 
                    value="{{ $empleado->id_empleado }}"
                    @selected(old('id_empleado', $registroV->id_empleado ?? null) == $empleado->id_empleado)
                >
                    {{ $empleado->nombre }}
                </option>
            @endforeach
        </select>
    @endif
    
    @error('id_empleado')
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
                            {!! $errors->first('lugarventa', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
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
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Items de Trabajo</h5>
                            <button type="button" class="btn btn-success btn-sm btn-add-work">
                                <i class="fas fa-plus-circle me-1"></i> Agregar Trabajo
                            </button>
                        </div>
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
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h5 class="mb-0">Costos Extras</h5>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success-subtle rounded p-1 m-2 border d-flex align-items-center me-2"> 
                                    <span class="mx-2 fw-medium text-bold" style="font-size: 1rem">% Cerrajero:</span>
                                    <div class="input-group input-group-sm" style="width: 90px;">
                                        <span class="input-group-text bg-light border-0 py-1 px-2 text-bold">$</span>
                                        <input type="text" name="porcentaje_c" 
                                               class="form-control form-control-sm text-end border-0 py-1"
                                               value="{{ old('porcentaje_c', $registroV?->porcentaje_c ?? 0) }}" 
                                               id="porcentaje_c"
                                               readonly
                                               style="background-color: #f8f9fa; font-size: 1.1rem"> 
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm btn-add-costo">
                                    <i class="fas fa-plus-circle me-1"></i> Agregar Costo
                                </button>
                            </div>                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="costos-container">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 6: Gastos -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Gastos</h5>
                            <button type="button" class="btn btn-success btn-sm btn-add-gasto">
                                <i class="fas fa-plus-circle me-1"></i> Agregar Gasto
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="gastos-container">
                            <!-- Los gastos dinámicos se insertarán aquí -->
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
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="valor_v" class="form-label">{{ __('Valor') }}</label>
                                    <input type="text" name="valor_v" class="form-control @error('valor_v') is-invalid @enderror" 
                                        value="{{ old('valor_v', $registroV?->valor_v) }}" id="valor_v" placeholder="Valor">
                                    {!! $errors->first('valor_v', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="titular_c" class="form-label">{{ __('Titular') }}</label>
                                    <input type="text" name="titular_c" class="form-control @error('titular_c') is-invalid @enderror" 
                                        value="{{ old('titular_c', $registroV?->titular_c) }}" id="titular_c" placeholder="Titular">
                                    {!! $errors->first('titular_c', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="tipo_venta" class="form-label">{{ __('Tipo de Venta') }}</label>
                                    <select name="tipo_venta" id="tipo_venta" class="form-control @error('tipo_venta') is-invalid @enderror">
                                        <option value="">Tipo de Venta</option>
                                        <option value="contado" {{ old('tipo_venta', $registroV?->tipo_venta) == 'contado' ? 'selected' : '' }}>Contado</option>
                                        <option value="credito" {{ old('tipo_venta', $registroV?->tipo_venta) == 'credito' ? 'selected' : '' }}>Crédito</option>
                                    </select>
                                    {!! $errors->first('tipo_venta', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
                                </div>
                            </div>                                                  
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="estatus" class="form-label">{{ __('Estatus') }}</label>
                                    <select name="estatus" id="estatus" class="form-control @error('estatus') is-invalid @enderror" readonly>
                                        <option value="">Estado</option>
                                        <option value="pagado" {{ old('estatus', $registroV?->estatus) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                        <option value="pendiente" {{ old('estatus', $registroV?->estatus) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="parcialemente pagado" {{ old('estatus', $registroV?->estatus) == 'parcialemente pagado' ? 'selected' : '' }}>Parcial</option>
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
                                <select id="pago_metodo" name="pago_metodo" class="form-control">
                                    <option value="">Seleccione método de pago</option>
                                    @foreach($tiposDePago as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
                                    @endforeach
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

.costo-group {
    background-color: #f8f9fa;
    margin-bottom: 15px;
    border-radius: 5px;
}

.costo-group:hover {
    background-color: #e9ecef;
}

.btn-remove-costo {
    transition: all 0.2s ease;
}

.btn-remove-costo:hover {
    transform: scale(1.1);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        dropdownAutoWidth: true,
    });

    /**************************************
     * SECCIÓN DE PAGOS PARCIALES
     **************************************/
    let valorTotal = parseFloat($('#valor_v').val()) || 0;
    let totalPagado = 0;
    let saldoPendiente = valorTotal;

    const metodosPago = @json($tiposDePago ?? []);

    actualizarResumen();

    $('#valor_v').on('change', function() {
        valorTotal = parseFloat($(this).val()) || 0;
        actualizarResumen();
        actualizarMaximoPago();
        calcularPorcentajesCostos();
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
            const pagos = JSON.parse(pagosJson);
            return pagos.reduce((total, pago) => total + parseFloat(pago.monto), 0);
        } catch (e) {
            return 0;
        }
    }

    function actualizarEstatus() {
        if (saldoPendiente <= 0.01) { 
            $('#estatus').val('pagado');
        } else if (totalPagado > 0) {
            $('#estatus').val('parcialemente pagado'); 
        } else {
            $('#estatus').val('pendiente');
        }
    }

    $('#btn-agregar-pago').click(function() {
        const monto = parseFloat($('#pago_monto').val());
        const metodoId = $('#pago_metodo').val();
        const fecha = $('#pago_fecha').val();
        
        if (!monto || monto <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Monto inválido',
                html: 'Por favor ingrese un <b>monto válido</b> mayor a cero',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_monto').val('').focus();
            });
            return;
        }
        
        if (monto > saldoPendiente) {
            Swal.fire({
                icon: 'error',
                title: 'Saldo insuficiente',
                html: `El monto excede el saldo pendiente de <strong>$${saldoPendiente.toFixed(2)}</strong>`,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_monto').val(saldoPendiente.toFixed(2)).focus();
            });
            return;
        }
        
        if (!metodoId) {
            Swal.fire({
                icon: 'error',
                title: 'Método requerido',
                html: 'Por favor seleccione un <b>método de pago</b>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_metodo').focus();
            });
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
            metodo_pago: metodoId,
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
                const metodo = metodosPago.find(m => m.id == pago.metodo_pago);
                const nombreMetodo = metodo ? metodo.name : pago.metodo_pago;
                
                $('#lista-pagos').append(`
                    <div class="pago-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">$${parseFloat(pago.monto).toFixed(2)}</span>
                                    <span class="text-muted ms-2">(${nombreMetodo})</span>
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

    /**************************************
     * SECCIÓN DE CLIENTES (SELECT2)
     **************************************/
    $('#id_cliente').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un cliente',
        allowClear: true
    });

    const telefonoInput = document.getElementById('telefono');

    $('#id_cliente').on('change', function() {
        const selectedOption = $(this).find(':selected');
        telefonoInput.value = selectedOption.data('telefono') || '';
    });

    if ($('#id_cliente').val()) {
        const initialOption = $('#id_cliente').find(':selected');
        telefonoInput.value = initialOption.data('telefono') || '';
    }


    /**************************************
     * SECCIÓN DE COSTOS EXTRAS 
     **************************************/
    let costoIndex = {{ count($costosExtras ?? []) }};
    const costosExistentes = @json($costosExtras ?? []);

    function addNewCostoGroup(costoData = null) {
        const currentIndex = costoIndex;
        const isExisting = costoData !== null;

        let metodoPagoOptions = '<option value="">Seleccione método</option>';
        @foreach($tiposDePago as $tipo)
            metodoPagoOptions += `<option value="{{ $tipo->id }}" ${isExisting && costoData.metodo_pago == '{{ $tipo->id }}' ? 'selected' : ''}>{{ $tipo->name }}</option>`;
        @endforeach

        const newCostoGroup = $(`
            <div class="costo-group mb-4 p-3 border rounded" data-index="${currentIndex}">
                <input type="hidden" name="costos_extras[${currentIndex}][id_costos]" value="${isExisting ? (costoData.id_costos || '') : ''}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="costos_extras[${currentIndex}][descripcion]" 
                                class="form-control descripcion-ce" 
                                value="${isExisting ? (costoData.descripcion || '') : ''}" 
                                >
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" step="0.01" name="costos_extras[${currentIndex}][monto]" 
                                class="form-control monto-ce" 
                                value="${isExisting ? (costoData.monto || '0.00') : '0.00'}" 
                                >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="costos_extras[${currentIndex}][metodo_pago]" 
                                    class="form-control metodo-pago select2" >
                                ${metodoPagoOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Estado</label>
                            <select name="costos_extras[${currentIndex}][cobro]" class="form-control">
                                <option value="pendiente" ${isExisting && costoData.cobro == 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="pagado" ${isExisting && costoData.cobro == 'pagado' ? 'selected' : ''}>Pagado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-costo mt-4">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </div>
                </div>
                ${isExisting && costoData.fecha ? `<input type="hidden" name="costos_extras[${currentIndex}][fecha]" value="${costoData.fecha}">` : ''}
            </div>
        `);

        $('#costos-container').append(newCostoGroup);

        newCostoGroup.find('.metodo-pago').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        
        costoIndex++;
    }

    function calcularTotalCostos() {
        let total = 0;
        $('.monto-ce').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        return total;
    }

    $(document).on('click', '.btn-add-costo', function() {
        addNewCostoGroup();
    });

    $(document).on('click', '.btn-remove-costo', function() {
        $(this).closest('.costo-group').remove();
        calcularPorcentajeCerrajero();
    });

    $(document).on('input', '.monto-ce, #valor_v', function() {
        calcularPorcentajeCerrajero();
    });

    $(document).ready(function() {
        if (costosExistentes && costosExistentes.length > 0) {
            costosExistentes.forEach(costo => {
                addNewCostoGroup(costo);
            });
        } else {
            addNewCostoGroup(); 
        }

        calcularPorcentajeCerrajero();
    });


    /**************************************
     * SECCIÓN DE GASTOS 
     **************************************/
    let gastoIndex = {{ count($gastosData ?? []) }};
    const gastosExistentes = @json($gastosData ?? []);

    function addNewGastoGroup(gastoData = null) {
        const currentIndex = gastoIndex;
        const isExisting = gastoData !== null;

        let metodoPagoOptions = '<option value="">Seleccione método</option>';
        @foreach($tiposDePago as $tipo)
            metodoPagoOptions += `<option value="{{ $tipo->id }}" ${isExisting && gastoData.metodo_pago == '{{ $tipo->id }}' ? 'selected' : ''}>{{ $tipo->name }}</option>`;
        @endforeach

        const newGastoGroup = $(`
            <div class="gasto-group mb-4 p-3 border rounded" data-index="${currentIndex}">
                <input type="hidden" name="gastos[${currentIndex}][id_gastos]" value="${isExisting ? (gastoData.id_gastos || '') : ''}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="gastos[${currentIndex}][descripcion]" 
                                class="form-control descripcion-gasto" 
                                value="${isExisting ? (gastoData.descripcion || '') : ''}" 
                                >
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" step="0.01" name="gastos[${currentIndex}][monto]" 
                                class="form-control monto-gasto" 
                                value="${isExisting ? (gastoData.valor || '0.00') : '0.00'}" 
                                >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="gastos[${currentIndex}][metodo_pago]" 
                                    class="form-control metodo-pago-gasto select2" >
                                ${metodoPagoOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Estado</label>
                            <select name="gastos[${currentIndex}][estatus]" class="form-control">
                                <option value="pendiente" ${isExisting && gastoData.estatus == 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="pagado" ${isExisting && gastoData.estatus == 'pagado' ? 'selected' : ''}>Pagado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-gasto mt-4">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </div>
                </div>
                ${isExisting && gastoData.fecha ? `<input type="hidden" name="gastos[${currentIndex}][fecha]" value="${gastoData.fecha}">` : ''}
            </div>
        `);

        $('#gastos-container').append(newGastoGroup);

        newGastoGroup.find('.metodo-pago-gasto').select2({
            width: '100%',
            dropdownAutoWidth: true
        });

        newGastoGroup.find('.monto-gasto').trigger('input');
        
        gastoIndex++;
    }

    function calcularTotalGastos() {
        let total = 0;
        $('.monto-gasto').each(function() {
            const val = parseFloat($(this).val());
            if (!isNaN(val)) total += val;
        });
        console.log("Total gastos calculado:", total); 
        return total;
    }

    function calcularPorcentajeCerrajero() {
        const totalCostos = calcularTotalCostos();
        const totalGastos = calcularTotalGastos();
        const valorVenta = parseFloat($('#valor_v').val()) || 0;
        const porcentaje = (valorVenta - totalCostos - totalGastos) * 0.36;
        $('#porcentaje_c').val(porcentaje.toFixed(2));
    }

    $(document).on('click', '.btn-add-gasto', function() {
        addNewGastoGroup();
    });

    $(document).on('click', '.btn-remove-gasto', function() {
        $(this).closest('.gasto-group').remove();
        calcularPorcentajeCerrajero();
    });

    $(document).on('input', '.monto-gasto, #valor_v', function() {
        calcularPorcentajeCerrajero();
    });

    $(document).ready(function() {
        if (gastosExistentes && gastosExistentes.length > 0) {
            gastosExistentes.forEach(gasto => {
                const formattedGasto = {
                    id_gastos: gasto.id_gastos,
                    descripcion: gasto.descripcion,
                    valor: gasto.monto || gasto.valor,
                    monto: gasto.monto || gasto.valor,
                    metodo_pago: gasto.metodo_pago,
                    estatus: gasto.estatus,
                    fecha: gasto.fecha
                };
                addNewGastoGroup(formattedGasto);
            });
        } else {
            addNewGastoGroup();
        }

        setTimeout(() => {
            calcularPorcentajeCerrajero();
        }, 300);

    });
    /**************************************
     * SECCIÓN DE ITEMS DE TRABAJO
     **************************************/
    let itemGroupIndex = 0;
    const itemsExistentes = @json($registroV->items ?? []);

    function verificarStock() {
        let sinStock = false;
        let mensajesError = [];
        
        $('.producto-row').each(function() {
            const $row = $(this);
            const productoSelect = $row.find('select[name$="[producto]"]');
            const cantidadInput = $row.find('input[name$="[cantidad]"]');
            const almacenSelect = $row.find('select[name$="[almacen]"]');
            
            const productoId = productoSelect.val();
            const cantidad = parseInt(cantidadInput.val()) || 0;
            const almacenId = almacenSelect.val();
            
            if (productoId && almacenId && cantidad > 0) {
                $.ajax({
                    url: '/verificar-stock',
                    type: 'GET',
                    async: false, 
                    data: {
                        producto_id: productoId,
                        almacen_id: almacenId,
                        cantidad: cantidad
                    },
                    success: function(response) {
                        if (!response.suficiente) {
                            sinStock = true;
                            mensajesError.push(`No hay suficiente stock para el producto ${productoSelect.find('option:selected').text()}. Stock disponible: ${response.stock}`);
                        }
                    },
                    error: function() {
                        console.error('Error al verificar el stock');
                    }
                });
            }
        });
        
        return {
            valido: !sinStock,
            mensajes: mensajesError
        };
    }

    $('form').on('submit', function(e) {
        e.preventDefault();
        
        const verificacionStock = verificarStock();
        
        if (!verificacionStock.valido) {
            Swal.fire({
                title: 'Error de Stock',
                html: verificacionStock.mensajes.join('<br>'),
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        this.submit();
    });

    function cargarProductosEnSelect($select, idAlmacen, productoSeleccionado = null, nombreProducto = null, precio = null) {
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
                                <option 
                                    value="${productoEncontrado.id_producto}" 
                                    data-precio="${productoEncontrado.precio_venta || productoEncontrado.precio || '0'}"
                                    data-stock="${productoEncontrado.stock || 0}"
                                    selected>
                                    ${productoEncontrado.id_producto} - ${productoEncontrado.item}
                                </option>`;
                        } else if (nombreProducto) {
                            options += `
                                <option 
                                    value="${productoSeleccionado}" 
                                    data-precio="${precio || '0'}"
                                    data-stock="0"
                                    selected>
                                    ${productoSeleccionado} - ${nombreProducto}
                                </option>`;
                        }
                    }
                    
                    response.forEach(function(producto) {
                        if (producto.id_producto != productoSeleccionado) {
                            options += `
                                <option 
                                    value="${producto.id_producto}" 
                                    data-precio="${producto.precio_venta || producto.precio || '0'}"
                                    data-stock="${producto.stock || 0}">
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
                            <option 
                                value="${productoSeleccionado}" 
                                data-precio="${precio || '0'}"
                                data-stock="0"
                                selected>
                                ${productoSeleccionado} - ${nombreProducto}
                            </option>
                            <option value="">{{ __('Select Producto') }}</option>
                        `).prop('disabled', false);
                        $select.select2();
                    }
                }
            });
        }
    }

    $(document).on('change', 'input[name$="[cantidad]"]', function() {
        const $row = $(this).closest('.producto-row');
        const productoSelect = $row.find('select[name$="[producto]"]');
        const cantidad = parseInt($(this).val()) || 0;
        
        if (productoSelect.val()) {
            const stockDisponible = parseInt(productoSelect.find('option:selected').data('stock')) || 0;
            
            if (cantidad > stockDisponible) {
                Swal.fire({
                    title: 'Stock Insuficiente',
                    text: `Solo hay ${stockDisponible} unidades disponibles de este producto`,
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });

                $(this).val(stockDisponible > 0 ? stockDisponible : 0);
            }
        }
    });

    $(document).on('change', 'select[name^="items"][name$="[producto]"]', function() {
        const $row = $(this).closest('.producto-row');
        const precioInput = $row.find('input[name$="[precio]"]');
        const precio = $(this).find('option:selected').data('precio') || '0';

        precioInput.val(precio);

        const nombreProducto = $(this).find('option:selected').text().split('-')[1]?.trim();
        $row.find('input[name$="[nombre_producto]"]').val(nombreProducto);
    });

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
                                ${productoData.codigo_producto || productoData.producto} - ${productoData.nombre_producto || 'Producto'}
                            </option>
                        ` : ''}
                    </select>
                    <input type="hidden" name="items[${itemGroupIndex}][productos][${productoIndex}][nombre_producto]" 
                        value="${productoData ? productoData.nombre_producto : ''}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Cantidad') }}</label>
                    <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][cantidad]"
                        class="form-control" placeholder="Cantidad" min="1"
                        value="${productoData ? productoData.cantidad : '0'}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Precio') }}</label>
                    <input type="number" step="0.01" name="items[${itemGroupIndex}][productos][${productoIndex}][precio]"
                            class="form-control" placeholder="Precio" min="0" readonly
                            value="${productoData ? productoData.precio : '0'}">
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
                productoData.nombre_producto
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