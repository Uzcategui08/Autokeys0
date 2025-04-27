<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header" style="background-color: #e3f2fd;">
                <h5 class="mb-0">Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="id_empleado" class="form-label fw-bold">{{ __('Empleado') }}</label>
                            <select name="id_empleado" class="form-control select2" id="id_empleado" required>
                                <option value="" selected>{{ __('Seleccionar Empleado') }}</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->id_empleado }}">{{ $emp->nombre }} - {{ $emp->cedula }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="button" id="buscarRegistros" class="btn btn-primary">
                        Buscar Registros
                    </button>
                </div>
            </div>
        </div>

        <div id="tablaResultados" class="card mb-4" style="display:none;">
            <div class="card-header" style="background-color: #e8f5e9;">
                <h5 class="mb-0">Registros encontrados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead style="background-color: #f5f5f5;">
                            <tr>
                                <th width="50px" class="text-center">Sel.</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th class="text-right">Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTabla">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="background-color: #fff8e1;">
                <h5 class="mb-0">Sueldo Base</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="sueldo_base" class="form-label">Ingrese el Sueldo Base</label>
                    <input type="number" step="0.01" class="form-control" id="sueldo_base" name="sueldo_base" value="0.00">
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="background-color: #f3e5f5;">
                <h5 class="mb-0">Resumen de Nómina</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_descuentos" class="form-label">Total Descuentos</label>
                            <input type="text" class="form-control text-danger fw-bold" id="total_descuentos" name="total_descuentos" readonly style="background-color: #ffebee;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_abonos" class="form-label">Total Abonos</label>
                            <input type="text" class="form-control text-success fw-bold" id="total_abonos" name="total_abonos" readonly style="background-color: #e8f5e9;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="total_pagado" class="form-label">Total a Pagar</label>
                            <input type="text" class="form-control text-white fw-bold" id="total_pagado" name="total_pagado" value="0.00" readonly style="background-color: #bbdefb;">
                        </div>
                    </div>
                </div>

                <div class="row mt-3" id="distribucion-pagos" style="display: none;">
                    <div class="col-md-12">
                        <h5>Distribución del Pago</h5>
                        <div class="table-responsive">
                            <table class="table" style="background-color: #fafafa;">
                                <thead>
                                    <tr style="background-color: #f5f5f5;">
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="metodos-pago-body">

                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f5f5f5;">
                                        <td class="text-right fw-bold">Total Distribuido:</td>
                                        <td id="total-distribuido" class="fw-bold">$0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr style="background-color: #f5f5f5;">
                                        <td class="text-right fw-bold">Restante:</td>
                                        <td id="restante-pagar" class="fw-bold">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="agregar-metodo">
                            Agregar Método
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="id_nempleado" name="id_nempleado" value="{{ $nempleado?->id_nempleado ?? '' }}">

        <div class="col-md-12 mt-4 text-center">
            <button type="submit" class="btn btn-success">
                Guardar Nómina
            </button>
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
    
    .card-header {
        padding: 12px 16px;
        font-weight: 500;
    }
    
    .table {
        margin-bottom: 0;
        border: 1px solid #eee;
    }
    
    .table th {
        font-weight: 500;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
    }
    
    .table td {
        border-top: 1px solid #eee;
        vertical-align: middle;
    }
    
    .badge {
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 3px;
    }
    
    .badge-abono {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    
    .badge-descuento {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    .form-control {
        border-radius: 3px;
    }
    
    .btn {
        border-radius: 3px;
        padding: 8px 16px;
    }
    
    .text-right {
        text-align: right;
    }
    
    .fw-bold {
        font-weight: 500;
    }
    
    #total_pagado {
        background-color: #bbdefb;
        border-color: #90caf9;
    }
    
    #total_abonos {
        background-color: #e8f5e9;
        border-color: #c8e6c9;
    }
    
    #total_descuentos {
        background-color: #ffebee;
        border-color: #ffcdd2;
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px;
        border-radius: 3px;
        border: 1px solid #ced4da;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    const metodosPago = {
        1: 'Transferencia Bancaria',
        2: 'Cheque',
        3: 'Efectivo',
        4: 'Otro'
    };
    
    let abonosSeleccionados = [];
    let descuentosSeleccionados = [];
    let metodosPagoSeleccionados = [];
    let sueldoBaseManual = 0;

    function init() {
        $('.select2').select2({
            placeholder: "Seleccione un empleado",
            allowClear: true
        });

        $('#id_empleado').change(function() {
            const empleadoId = $(this).val();
            if (empleadoId) {
                $.get('/empleados/' + empleadoId + '/sueldo')
                    .done(response => {
                        if (response.success) {
                            $('#sueldo_base').val(response.sueldo_base);
                        }
                    })
                    .fail(xhr => console.error("Error al obtener sueldo:", xhr.responseText));
            }
        });
    }

    function calcularTotalGeneral() {
        let totalAbonos = 0;
        let totalDescuentos = 0;
        abonosSeleccionados = [];
        descuentosSeleccionados = [];

        $('.check-abono:checked').each(function() {
            const id = $(this).data('id');
            const monto = parseFloat($(this).data('monto')) || 0;

            if (id !== undefined && id !== null && id !== "") {
                abonosSeleccionados.push(id);
                totalAbonos += monto;
            }
        });

        $('.check-descuento:checked').each(function() {
            const id = $(this).data('id');
            const monto = parseFloat($(this).data('monto')) || 0;

            if (id !== undefined && id !== null && id !== "") {
                descuentosSeleccionados.push(id);
                totalDescuentos += monto;
            }
        });

        $('#id_abonos_json').val(JSON.stringify(abonosSeleccionados));
        $('#id_descuentos_json').val(JSON.stringify(descuentosSeleccionados));

        $('#total_abonos').val(formatCurrency(totalAbonos));
        $('#total_descuentos').val(formatCurrency(totalDescuentos));

        const totalPagar = sueldoBaseManual + totalAbonos - totalDescuentos;
        $('#total_pagado').val(formatCurrency(Math.max(totalPagar, 0)));
        
        actualizarDistribucionPagos();
    }

    $('form').submit(function(e) {

    const totalPagar = parseFloat($('#total_pagado').val().replace(/[^0-9.-]+/g,"")) || 0;
    const totalDistribuido = parseFloat($('#total-distribuido').text().replace(/[^0-9.-]+/g,"")) || 0;
    
    if (totalPagar > 0 && totalDistribuido !== totalPagar) {
        e.preventDefault();
        const restante = totalPagar - totalDistribuido;
        
        Swal.fire({
            title: 'Distribución incompleta',
            html: `El total distribuido ($${formatCurrency(totalDistribuido)}) no coincide con el total a pagar ($${formatCurrency(totalPagar)}).<br>
                  <strong>Faltan distribuir: $${formatCurrency(Math.abs(restante))}</strong>`,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        return false;
    }

        $('<input>').attr({
            type: 'hidden',
            name: 'id_abonos_json',
            value: JSON.stringify(abonosSeleccionados)
        }).appendTo(this);

        $('<input>').attr({
            type: 'hidden',
            name: 'id_descuentos_json',
            value: JSON.stringify(descuentosSeleccionados)
        }).appendTo(this);

        $('<input>').attr({
            type: 'hidden',
            name: 'metodo_pago_json',
            value: JSON.stringify(metodosPagoSeleccionados.map(m => ({
                metodo_id: parseInt(m.metodo_id),
                monto: parseFloat(m.monto)
            })))
        }).appendTo(this);

        return true;
    });

    function actualizarDistribucionPagos() {
        const totalPagar = parseFloat($('#total_pagado').val().replace(/[^0-9.-]+/g,"")) || 0;
        
        if (totalPagar > 0) {
            $('#distribucion-pagos').show();
            
            if (metodosPagoSeleccionados.length === 0) {
                agregarMetodoPago(1, totalPagar);
            } else {
                calcularRestante();
            }
        } else {
            $('#distribucion-pagos').hide();
            metodosPagoSeleccionados = [];
            $('#metodos-pago-body').empty();
        }
    }

    function agregarMetodoPago(metodoId, monto = 0) {
        metodosPagoSeleccionados.push({
            id: Date.now(),
            metodo_id: metodoId,
            monto: monto
        });
        renderizarMetodosPago();
    }

    function renderizarMetodosPago() {
        $('#metodos-pago-body').empty();
        let totalDistribuido = 0;
        
        metodosPagoSeleccionados.forEach((metodo, index) => {
            totalDistribuido += parseFloat(metodo.monto) || 0;
            
            $('#metodos-pago-body').append(`
                <tr data-id="${metodo.id}">
                    <td>
                        <select class="form-control metodo-pago-select" data-index="${index}">
                            ${Object.entries(metodosPago).map(([id, nombre]) => `
                                <option value="${id}" ${id == metodo.metodo_id ? 'selected' : ''}>${nombre}</option>
                            `).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control monto-metodo" 
                               data-index="${index}" 
                               value="${parseFloat(metodo.monto).toFixed(2)}" 
                               step="0.01" min="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-metodo" data-index="${index}">
                            Eliminar
                        </button>
                    </td>
                </tr>`);
        });
        
        $('#total-distribuido').text(formatCurrency(totalDistribuido));
        calcularRestante();
    }

    function calcularRestante() {
        const totalPagar = parseFloat($('#total_pagado').val().replace(/[^0-9.-]+/g,"")) || 0;
        let totalDistribuido = 0;
        
        metodosPagoSeleccionados.forEach(metodo => {
            totalDistribuido += parseFloat(metodo.monto) || 0;
        });
        
        $('#total-distribuido').text(formatCurrency(totalDistribuido));
        $('#restante-pagar').text(formatCurrency(totalPagar - totalDistribuido));
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    function formatCurrency(amount) {
        return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    $('#buscarRegistros').click(function() {
        const empleadoId = $('#id_empleado').val();
        const fechaDesde = $('#fecha_desde').val();
        const fechaHasta = $('#fecha_hasta').val();

        if (!empleadoId || !fechaDesde || !fechaHasta) {
            Swal.fire('Error', 'Complete todos los filtros de búsqueda', 'error');
            return;
        }

        $('#cuerpoTabla').html(`
            <tr>
                <td colspan="5" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Buscando registros...</p>
                </td>
            </tr>
        `);
        $('#tablaResultados').show();

        $.get('{{ route("nomina.getRegistros") }}', {
            id_empleado: empleadoId,
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        })
        .done(response => {
            let html = '';
            let totalAbonos = 0;
            let totalDescuentos = 0;

            if (response.abonos?.length > 0) {
                response.abonos.forEach(abono => {
                    html += `
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="check-abono" data-id="${abono.id_abonos}" data-monto="${abono.valor}" checked>
                        </td>
                        <td><span class="badge badge-abono">Abono</span></td>
                        <td>${abono.concepto || 'Sin concepto'}</td>
                        <td class="text-right text-success font-weight-bold">
                            $${parseFloat(abono.valor).toFixed(2)}
                        </td>
                        <td>${abono.a_fecha ? formatDate(abono.a_fecha) : 'N/A'}</td>
                    </tr>`;
                    totalAbonos += parseFloat(abono.valor) || 0;
                });
            }

            if (response.descuentos?.length > 0) {
                response.descuentos.forEach(descuento => {
                    html += `
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="check-descuento" data-id="${descuento.id_descuentos}" data-monto="${descuento.valor}" checked>
                        </td>
                        <td><span class="badge badge-descuento">Descuento</span></td>
                        <td>${descuento.concepto || 'Sin concepto'}</td>
                        <td class="text-right text-danger font-weight-bold">
                            $${parseFloat(descuento.valor).toFixed(2)}
                        </td>
                        <td>${descuento.d_fecha ? formatDate(descuento.d_fecha) : 'N/A'}</td>
                    </tr>`;
                    totalDescuentos += parseFloat(descuento.valor) || 0;
                });
            }

            if (!html) {
                html = `<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron registros</td></tr>`;
            }

            $('#cuerpoTabla').html(html);
            $('#total_abonos').val(totalAbonos.toFixed(2));
            $('#total_descuentos').val(totalDescuentos.toFixed(2));
            calcularTotalGeneral();
        })
        .fail(xhr => {
            console.error("Error en AJAX:", xhr.responseText);
            $('#cuerpoTabla').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        Error al cargar los datos
                    </td>
                </tr>
            `);
        });
    });

    $('#sueldo_base').on('input', function() {
        sueldoBaseManual = parseFloat($(this).val()) || 0;
        calcularTotalGeneral();
    });

    $(document).on('change', '.check-abono, .check-descuento', calcularTotalGeneral);

    $('#agregar-metodo').click(() => agregarMetodoPago(1, 0));
    $(document).on('change', '.metodo-pago-select', function() {
        const index = $(this).data('index');
        metodosPagoSeleccionados[index].metodo_id = $(this).val();
    });
    $(document).on('input', '.monto-metodo', function() {
        const index = $(this).data('index');
        metodosPagoSeleccionados[index].monto = parseFloat($(this).val()) || 0;
        calcularRestante();
    });
    $(document).on('click', '.eliminar-metodo', function() {
        metodosPagoSeleccionados.splice($(this).data('index'), 1);
        renderizarMetodosPago();
    });

    init();
});
</script>