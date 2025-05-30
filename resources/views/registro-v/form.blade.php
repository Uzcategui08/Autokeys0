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

                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 offset-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">Total Trabajos:</span>
                                    <input type="text" class="form-control text-end fw-bold" id="total-trabajos" value="$0.00" readonly>
                                </div>
                            </div>
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
                            <div class="col-md-3">
                                <label class="form-label">Monto</label>
                                <input type="number" step="0.01" id="pago_monto" class="form-control" placeholder="0.00">
                                <small class="text-muted">Monto máximo: <span id="maximo-pago">${{ number_format($registroV->valor_v ?? 0, 2) }}</span></small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Método de Pago</label>
                                <select id="pago_metodo" name="pago_metodo" class="form-control">
                                    <option value="">Seleccione método de pago</option>
                                    @foreach($tiposDePago as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quién Cobró</label>
                                <select id="pago_cobrador" class="form-control">
                                    <option value="">Seleccionar técnico</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id_empleado }}">{{ $empleado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
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
                                                @if(isset($pago['cobrador_id']))
                                                    @php
                                                        $cobrador = $empleados->firstWhere('id_empleado', $pago['cobrador_id']);
                                                    @endphp
                                                    <small class="text-muted ms-2">Cobró: {{ $cobrador->nombre ?? 'Desconocido' }}</small>
                                                @endif
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
    const empleados = @json($empleados ?? []);

    actualizarResumen();

    $('#valor_v').on('change', function() {
        valorTotal = parseFloat($(this).val()) || 0;
        actualizarResumen();
        actualizarMaximoPago();
        calcularPorcentajesCostos();
        calcularPorcentajeCerrajero();
    });

    $('#estatus').on('change', function() {
        if(totalPagado === 0) {
            if($(this).val() === 'pagado') {
                agregarPagoCompleto();
            }
        }
    });

    function agregarPagoCompleto() {
        const cobradorId = $('#select_empleado').val();
        
        const pagosJson = JSON.stringify([{
            monto: valorTotal,
            metodo_pago: 'completo',
            fecha: new Date().toISOString().split('T')[0],
            cobrador_id: cobradorId
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
        const cobradorId = $('#pago_cobrador').val();
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

        if (!cobradorId) {
            Swal.fire({
                icon: 'error',
                title: 'Técnico requerido',
                html: 'Por favor seleccione <b>quien cobró</b> este pago',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            }).then(() => {
                $('#pago_cobrador').focus();
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
            fecha: fecha,
            cobrador_id: cobradorId
        });
        
        $('#pagos_json').val(JSON.stringify(pagos));
        
        actualizarListaPagos();
        actualizarResumen();
        actualizarMaximoPago();

        $('#pago_monto').val('');
        $('#pago_cobrador').val('').trigger('change');
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
                
                const cobrador = empleados.find(e => e.id_empleado == pago.cobrador_id);
                const nombreCobrador = cobrador ? cobrador.nombre : 'Desconocido';
                
                $('#lista-pagos').append(`
                    <div class="pago-item card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">$${parseFloat(pago.monto).toFixed(2)}</span>
                                    <span class="text-muted ms-2">(${nombreMetodo})</span>
                                    <small class="text-muted ms-2">${pago.fecha}</small>
                                    <small class="text-muted ms-2">Cobró: ${nombreCobrador}</small>
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

        let subcategoriaOptions = '<option value="">Seleccione subcategoría</option>';
        @foreach($categorias as $categoria)
            subcategoriaOptions += `<option value="{{ $categoria->id_categoria }}" ${isExisting && costoData.subcategoria == '{{ $categoria->id_categoria }}' ? 'selected' : ''}>{{ $categoria->nombre }}</option>`;
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
                                value="${isExisting ? (costoData.descripcion || '') : ''}">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" step="0.01" name="costos_extras[${currentIndex}][monto]" 
                                class="form-control monto-ce" 
                                value="${isExisting ? (costoData.monto || '0.00') : '0.00'}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Subcategoría</label>
                            <select name="costos_extras[${currentIndex}][subcategoria]" 
                                    class="form-control select2-subcategoria">
                                ${subcategoriaOptions}
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="costos_extras[${currentIndex}][metodo_pago]" 
                                    class="form-control metodo-pago select2">
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

        newCostoGroup.find('.select2-subcategoria').select2({
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

    function calcularPorcentajeCerrajero() {
        const valorVenta = parseFloat($('#valor_v').val()) || 0;
        const totalCostos = calcularTotalCostos(); 
        const totalGastos = calcularTotalGastos(); 

        console.log("Debug:", {
            valorVenta,
            totalCostos,
            totalGastos,
            formula: (valorVenta - totalCostos - totalGastos) * 0.36
        });

        const porcentaje = (valorVenta - totalCostos - totalGastos) * 0.36;
        $('#porcentaje_c').val(Math.max(0, porcentaje).toFixed(2));
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
                const formattedCosto = {
                    id_costos: costo.id_costos,
                    descripcion: costo.descripcion,
                    monto: costo.monto,
                    metodo_pago: costo.metodo_pago,
                    cobro: costo.cobro,
                    fecha: costo.fecha,
                    subcategoria: costo.subcategoria || costo.id_categoria
                };
                addNewCostoGroup(formattedCosto);
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

        let subcategoriaOptions = '<option value="">Seleccione subcategoría</option>';
        @foreach($categorias as $categoria)
            subcategoriaOptions += `<option value="{{ $categoria->id_categoria }}" ${isExisting && gastoData.subcategoria == '{{ $categoria->id_categoria }}' ? 'selected' : ''}>{{ $categoria->nombre }}</option>`;
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
                                value="${isExisting ? (gastoData.descripcion || '') : ''}">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" step="0.01" name="gastos[${currentIndex}][monto]" 
                                class="form-control monto-gasto" 
                                value="${isExisting ? (gastoData.valor || '0.00') : '0.00'}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Subcategoría</label>
                            <select name="gastos[${currentIndex}][subcategoria]" 
                                    class="form-control select2-subcategoria">
                                ${subcategoriaOptions}
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="gastos[${currentIndex}][metodo_pago]" 
                                    class="form-control metodo-pago-gasto select2">
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

        newGastoGroup.find('.select2-subcategoria').select2({
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
        return total;
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
                    fecha: gasto.fecha,
                    subcategoria: gasto.subcategoria || gasto.id_categoria 
                };
                addNewGastoGroup(formattedGasto);
            });
        } else {
            addNewGastoGroup();
        }

        setTimeout(() => {
        calcularTotalTrabajos();
        actualizarValorVenta();
        calcularPorcentajeCerrajero();
        }, 500);
    });


    /**************************************
     * SECCIÓN DE ITEMS DE TRABAJO 
     **************************************/
    let itemGroupIndex = 0;
    const itemsExistentes = @json($registroV->items ?? []);
    let totalTrabajos = 0;

    function verificarStock(ventaId = null) {
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
                        cantidad: cantidad,
                        venta_id: ventaId 
                    },
                    success: function(response) {
                        if (!response.suficiente) {
                            sinStock = true;
                            const productoNombre = productoSelect.find('option:selected').text().split('-')[1]?.trim();
                            const mensaje = response.cantidad_original > 0 ?
                                `<strong>${productoNombre}</strong>: 
                                Stock insuficiente para el cambio solicitado.<br>
                                Disponible: ${response.stock + response.cantidad_original} (${response.stock} en almacén + ${response.cantidad_original} de esta venta)<br>
                                Requerido: ${cantidad}` :
                                `<strong>${productoNombre}</strong>: 
                                Stock insuficiente en ${almacenSelect.find('option:selected').text()}.<br>
                                Disponible: ${response.stock} - Requerido: ${cantidad}`;
                            
                            mensajesError.push(`<div class="mb-2">${mensaje}</div>`);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al verificar el stock:', xhr.responseText);
                        sinStock = true;
                        mensajesError.push('Error al verificar disponibilidad de productos');
                    }
                });
            }
        });
        
        return {
            valido: !sinStock,
            mensajes: mensajesError
        };
    }

    function verificarStockSinInterferencia(ventaId = null) {
        let resultado = { valido: true, mensajes: [] };
        
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
                        cantidad: cantidad,
                        venta_id: ventaId
                    },
                    success: function(response) {
                        if (!response.suficiente) {
                            resultado.valido = false;
                            const productoNombre = productoSelect.find('option:selected').text().split('-')[1]?.trim();
                            resultado.mensajes.push(`<div class="mb-2">${productoNombre}: Stock insuficiente</div>`);
                        }
                    },
                    error: function() {
                        resultado.valido = false;
                        resultado.mensajes.push('Error al verificar stock');
                    }
                });
            }
        });
        
        return resultado;
    }

    $('form').on('submit', function(e) {
        e.preventDefault();

        const ventaId = {{ $registroV->id ?? 'null' }};
        const verificacion = verificarStock(ventaId);
        
        if (!verificacion.valido) {
            Swal.fire({
                title: 'Error de Stock',
                html: `
                    <div class="text-start">
                        <h5 class="text-danger">Productos con stock insuficiente:</h5>
                        ${verificacion.mensajes.join('')}
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33',
                scrollbarPadding: false
            });
            return false;
        }

        if (typeof prepararDatosParaEnvio === 'function') {
            prepararDatosParaEnvio();
        }
        
        this.submit();
    });

    $(document).on('change', 'input[name$="[cantidad]"]', function() {
        const $row = $(this).closest('.producto-row');
        const productoSelect = $row.find('select[name$="[producto]"]');
        const cantidad = parseInt($(this).val()) || 0;
        const almacenSelect = $row.find('select[name$="[almacen]"]');
        
        if (productoSelect.val() && almacenSelect.val() && cantidad > 0) {
            $.ajax({
                url: '/verificar-stock',
                type: 'GET',
                data: {
                    producto_id: productoSelect.val(),
                    almacen_id: almacenSelect.val(),
                    cantidad: cantidad
                },
                success: function(response) {
                    if (!response.suficiente) {
                        Swal.fire({
                            title: 'Stock Insuficiente',
                            html: `Solo hay ${response.stock} unidades disponibles`,
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
                        });
                        $(this).val(response.stock > 0 ? response.stock : 0);
                    }
                }.bind(this)
            });
        }
    });

    function cargarTrabajosEnSelect($select, trabajoSeleccionado = null) {
        return $.ajax({
            url: '/obtener-todos-trabajos',
            type: 'GET',
            dataType: 'json'
        }).then(function(response) {
            let options = '<option value="">{{ __("Seleccionar Trabajo") }}</option>';

            response.forEach(trabajo => {
                const selected = trabajoSeleccionado == trabajo.id_trabajo ? 'selected' : '';
                options += `<option value="${trabajo.id_trabajo}" ${selected} data-precio="${trabajo.precio || 0}">${trabajo.nombre}</option>`;
            });
            
            $select.html(options);

            $select.select2({
                placeholder: "Seleccione un trabajo",
                width: '100%',
                dropdownParent: $select.closest('.item-group')
            });

            return $select; 
        });
    }

    function calcularTotalTrabajos() {
        totalTrabajos = 0;
        
        $('.precio-trabajo').each(function() {
            const precio = parseFloat($(this).val()) || 0;
            totalTrabajos += precio;
        });
        
        $('#total-trabajos').val('$' + totalTrabajos.toFixed(2));
        actualizarValorVenta();
        calcularPorcentajeCerrajero(); 
    }

    function actualizarValorVenta() {
        const totalCostos = calcularTotalCostos();
        const totalGastos = calcularTotalGastos();
        const valorVenta = totalTrabajos;

        $('#valor_v').val(valorVenta.toFixed(2)).trigger('change');

        calcularPorcentajeCerrajero();
    }

    function cargarProductosEnSelect($select, idAlmacen, productoSeleccionado = null, nombreProducto = null, precio = null) {
        if (idAlmacen) {
            $.ajax({
                url: '/obtener-productos-orden',
                type: 'GET',
                data: { id_almacen: idAlmacen },
                success: function(response) {
                    let options = '<option value="">{{ __("Seleccionar Producto") }}</option>';
                    
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
                    $select.select2({
                        width: '100%',
                        dropdownParent: $select.closest('.item-group')
                    });
                    
                    if (productoSeleccionado) {
                        $select.val(productoSeleccionado).trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error('Error al cargar productos:', xhr.responseText);
                    if (productoSeleccionado && nombreProducto) {
                        $select.html(`
                            <option 
                                value="${productoSeleccionado}" 
                                data-precio="${precio || '0'}"
                                data-stock="0"
                                selected>
                                ${productoSeleccionado} - ${nombreProducto}
                            </option>
                            <option value="">{{ __("Seleccionar Producto") }}</option>
                        `).prop('disabled', false);
                        $select.select2({
                            width: '100%',
                            dropdownParent: $select.closest('.item-group')
                        });
                    }
                }
            });
        }
    }

    function addNewProductRow(itemGroup, productoData = null) {
        const productoIndex = itemGroup.find('.producto-row').length;
        const itemGroupIndex = itemGroup.data('index');

        const newProductoRow = `
            <div class="row mb-2 producto-row">
                <div class="col-md-4">
                    <label class="form-label">{{ __("Producto") }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][producto]"
                            class="form-control select2-producto" ${productoData ? '' : 'disabled'}>
                        <option value="">{{ __("Seleccionar Producto") }}</option>
                        ${productoData ? `
                            <option value="${productoData.producto}" selected
                                    data-precio="${productoData.precio || 0}"
                                    data-stock="${productoData.stock || 0}">
                                ${productoData.codigo_producto || productoData.producto} - ${productoData.nombre_producto || 'Producto'}
                            </option>
                        ` : ''}
                    </select>
                    <input type="hidden" name="items[${itemGroupIndex}][productos][${productoIndex}][nombre_producto]" 
                        value="${productoData ? productoData.nombre_producto : ''}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __("Cantidad") }}</label>
                    <input type="number" name="items[${itemGroupIndex}][productos][${productoIndex}][cantidad]"
                        class="form-control cantidad-producto" placeholder="Cantidad" min="1"
                        value="${productoData ? productoData.cantidad : '0'}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __("Precio") }}</label>
                    <input type="number" step="0.01" name="items[${itemGroupIndex}][productos][${productoIndex}][precio]"
                        class="form-control precio-producto" placeholder="Precio" min="0"
                        value="${productoData ? productoData.precio : '0'}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __("Almacén") }}</label>
                    <select name="items[${itemGroupIndex}][productos][${productoIndex}][almacen]"
                            class="form-control select-almacen">
                        <option value="">{{ __("Seleccionar Almacén") }}</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id_almacen }}"
                                ${productoData && productoData.almacen == '{{ $almacen->id_almacen }}' ? 'selected' : ''}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-remove-producto mt-4">
                        <i class="fa fa-minus-circle" aria-hidden="true"></i>
                    </button>
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
                productoData.precio
            );
        }
        
        $newRow.find('.select2-producto').select2({
            placeholder: "Seleccione un producto",
            width: '100%',
            dropdownParent: $newRow.closest('.item-group')
        });
    }

    function addNewItemGroup(itemData = null) {
        const currentIndex = itemData && itemData.index !== undefined ? itemData.index : itemGroupIndex;
        const trabajoValue = itemData ? (itemData.trabajo_id || itemData.trabajo || '') : '';
        const trabajoNombre = itemData ? (itemData.trabajo_nombre || itemData.trabajo || '') : '';
        const precioTrabajo = itemData ? (itemData.precio_trabajo || '0') : '0';
        const descripcion = itemData ? (itemData.descripcion || '') : '';

        const newItemGroup = $(`
            <div class="item-group mb-4 p-3 border rounded" data-index="${currentIndex}">
                <div class="row mb-2">
                    <div class="col-md-5">
                        <label class="form-label">{{ __("Trabajo") }}</label>
                        <select name="items[${currentIndex}][trabajo]" class="form-control select2-trabajo">
                            <option value="">{{ __("Seleccionar Trabajo") }}</option>
                            ${itemData && itemData.trabajo && !itemData.trabajo_id ? 
                            `<option value="${itemData.trabajo}" selected>${itemData.trabajo}</option>` : ''}
                        </select>
                        <input type="hidden" name="items[${currentIndex}][trabajo_id]" value="${trabajoValue}">
                        <input type="hidden" name="items[${currentIndex}][trabajo_nombre]" value="${trabajoNombre}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __("Precio Trabajo") }}</label>
                        <input type="number" step="0.01" name="items[${currentIndex}][precio_trabajo]" 
                            class="form-control precio-trabajo" placeholder="0.00" min="0"
                            value="${precioTrabajo}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __("Descripción") }}</label>
                        <input type="text" name="items[${currentIndex}][descripcion]" 
                            class="form-control" placeholder="Descripción adicional"
                            value="${descripcion}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-remove-item-group mt-4">
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="productos-container"></div>
                <button type="button" class="btn btn-success btn-add-producto mt-2">
                    {{ __("Agregar Producto") }}
                </button>
            </div>
        `);

        $('#items-container').append(newItemGroup);
        
        const $selectTrabajo = newItemGroup.find('.select2-trabajo');

        if (!itemData || itemData.trabajo_id) {
            itemGroupIndex++;
            cargarTrabajosEnSelect($selectTrabajo, trabajoValue);
        }

        if (itemData && itemData.trabajo && !itemData.trabajo_id) {
            $selectTrabajo.append(`<option value="${itemData.trabajo}" selected>${itemData.trabajo}</option>`);
            newItemGroup.find('input[name$="[trabajo_nombre]"]').val(itemData.trabajo);
        }

        $selectTrabajo.select2({
            placeholder: "Seleccione un trabajo",
            width: '100%',
            dropdownParent: $selectTrabajo.closest('.item-group')
        });

        if (itemData && itemData.productos) {
            itemData.productos.forEach(producto => {
                addNewProductRow(newItemGroup, producto);
            });
        }
        
        itemGroupIndex++;
        calcularTotalTrabajos();
        calcularPorcentajeCerrajero();
    }

    function prepararDatosParaEnvio() {
        $('.item-group').each(function() {
            const $group = $(this);
            const $selectTrabajo = $group.find('select[name$="[trabajo]"]');
            const trabajoId = $selectTrabajo.val();
            const trabajoNombre = $selectTrabajo.find('option:selected').text();

            $group.find('input[name$="[trabajo_id]"]').val(trabajoId);
            $group.find('input[name$="[trabajo_nombre]"]').val(trabajoNombre);
        });
    }

    function mostrarError(mensaje) {
        Swal.fire({
            title: '{{ __("Error") }}',
            text: mensaje,
            icon: 'error',
            confirmButtonText: '{{ __("Entendido") }}'
        });
    }

    $(document).on('change', '.select2-trabajo', function() {
        const $group = $(this).closest('.item-group');
        const precioTrabajo = $(this).find('option:selected').data('precio') || 0;
        $group.find('.precio-trabajo').val(precioTrabajo);
        
        const trabajoId = $(this).val();
        const trabajoNombre = $(this).find('option:selected').text();
        
        $group.find('input[name$="[trabajo_id]"]').val(trabajoId);
        $group.find('input[name$="[trabajo_nombre]"]').val(trabajoNombre);
        
        calcularTotalTrabajos();
    });

    $(document).on('input change', '.precio-trabajo, .monto-ce, .monto-gasto, #valor_v', function() {
        calcularPorcentajeCerrajero();
    });

    $(document).on('change', '.select2-trabajo', function() {
        calcularTotalTrabajos();
        calcularPorcentajeCerrajero();
    });

    $(document).on('input', '.precio-trabajo, .costo-input, .gasto-input', function() {
        calcularTotalTrabajos();
        calcularPorcentajeCerrajero(); 
    });

    $(document).on('change', '.select2-producto', function() {
        const $row = $(this).closest('.producto-row');
        const precioInput = $row.find('.precio-producto');
        const precio = $(this).find('option:selected').data('precio') || '0';
        precioInput.val(precio);

        const nombreCompleto = $(this).find('option:selected').text();
        const nombreProducto = nombreCompleto.includes('-') 
            ? nombreCompleto.split('-').slice(1).join('-').trim() 
            : nombreCompleto;
        $row.find('input[name$="[nombre_producto]"]').val(nombreProducto);
    });

    $(document).on('change', '.select-almacen', function() {
        const $row = $(this).closest('.producto-row');
        const $selectProducto = $row.find('.select2-producto');
        const productoSeleccionado = $selectProducto.val();
        const nombreCompleto = $(this).find('option:selected').text();
        const nombreProducto = nombreCompleto.includes('-') 
            ? nombreCompleto.split('-').slice(1).join('-').trim() 
            : nombreCompleto;
        const precioActual = $row.find('.precio-producto').val();
        
        cargarProductosEnSelect(
            $selectProducto, 
            $(this).val(), 
            productoSeleccionado,
            nombreProducto,
            precioActual
        );
    });

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
        calcularTotalTrabajos(); 
    });

    $(document).ready(function() {
        if (itemsExistentes && itemsExistentes.length > 0) {
            console.log('Items a cargar:', itemsExistentes);
            
            $('#items-container').empty();
            
            itemsExistentes.forEach((item, index) => {
                console.log('Cargando item', index, item);
                
                const newItemGroup = $(`
                    <div class="item-group mb-4 p-3 border rounded" data-index="${index}">
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <label class="form-label">{{ __("Trabajo") }}</label>
                                <select name="items[${index}][trabajo]" class="form-control select2-trabajo">
                                    <option value="">{{ __("Seleccionar Trabajo") }}</option>
                                    ${item.trabajo && !item.trabajo_id ? 
                                    `<option value="${item.trabajo}" selected>${item.trabajo}</option>` : ''}
                                </select>
                                <input type="hidden" name="items[${index}][trabajo_id]" value="${item.trabajo_id || ''}">
                                <input type="hidden" name="items[${index}][trabajo_nombre]" value="${item.trabajo_nombre || item.trabajo || ''}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __("Precio Trabajo") }}</label>
                                <input type="number" step="0.01" name="items[${index}][precio_trabajo]" 
                                    class="form-control precio-trabajo" placeholder="0.00" min="0"
                                    value="${item.precio_trabajo || '0'}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __("Descripción") }}</label>
                                <input type="text" name="items[${index}][descripcion]" 
                                    class="form-control" placeholder="Descripción adicional"
                                    value="${item.descripcion || ''}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-remove-item-group mt-4">
                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="productos-container"></div>
                        <button type="button" class="btn btn-success btn-add-producto mt-2">
                            {{ __("Agregar Producto") }}
                        </button>
                    </div>
                `);

                $('#items-container').append(newItemGroup);
                
                const $selectTrabajo = newItemGroup.find('.select2-trabajo');
                const $precioTrabajo = newItemGroup.find('.precio-trabajo');

                $selectTrabajo.select2({
                    placeholder: "Seleccione un trabajo",
                    width: '100%',
                    dropdownParent: $selectTrabajo.closest('.item-group')
                });

                if (item.trabajo_id) {
                    $selectTrabajo.off('change');
                    
                    cargarTrabajosEnSelect($selectTrabajo, item.trabajo_id).then(() => {
                        $selectTrabajo.val(item.trabajo_id);

                        $selectTrabajo.trigger('change.select2');

                        $precioTrabajo.val(item.precio_trabajo || '0');

                        setTimeout(() => {
                            $selectTrabajo.on('change', function() {
                                const precio = $(this).find('option:selected').data('precio') || 0;
                                $(this).closest('.item-group').find('.precio-trabajo').val(precio);
                                calcularTotalTrabajos();
                            });
                        }, 100);
                    });
                } 
                else if (item.trabajo) {
                    $selectTrabajo.append(`<option value="${item.trabajo}" selected>${item.trabajo}</option>`);
                    newItemGroup.find('input[name$="[trabajo_nombre]"]').val(item.trabajo);
                    $precioTrabajo.val(item.precio_trabajo || '0');
                }

                $selectTrabajo.on('change', function() {
                    const precio = $(this).find('option:selected').data('precio') || 0;
                    $(this).closest('.item-group').find('.precio-trabajo').val(precio);
                    calcularTotalTrabajos();
                });

                if (item.productos && item.productos.length > 0) {
                    item.productos.forEach(producto => {
                        addNewProductRow(newItemGroup, producto);
                    });
                }
            });
            
            itemGroupIndex = itemsExistentes.length;
        } else {
            addNewItemGroup();
        }
        
        calcularTotalTrabajos();
        calcularPorcentajeCerrajero();
    });

    function mostrarError(mensaje) {
        Swal.fire({
            title: '{{ __("Error") }}',
            text: mensaje,
            icon: 'error',
            confirmButtonText: '{{ __("Entendido") }}'
        });
    }
});
</script>