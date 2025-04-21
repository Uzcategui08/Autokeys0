@extends('adminlte::page')

@section('title', 'Reporte de Nóminas')

@section('content_header')
    <h1>Reporte de Nóminas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form id="filtroForm">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="periodo_id">Período de Nómina</label>
                        <select name="periodo_id" id="periodo_id" class="form-control select2" required>
                            <option value="">Seleccione un período</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id_pnomina }}" 
                                    {{ request('periodo_id') == $periodo->id_pnomina ? 'selected' : '' }}>
                                    {{ $periodo->tnomina->nombre }} ({{ \Carbon\Carbon::parse($periodo->inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periodo->fin)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empleado_id">Empleado</label>
                        <select name="empleado_id" id="empleado_id" class="form-control">
                            <option value="">Todos los empleados</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}" 
                                    {{ request('empleado_id') == $empleado->id_empleado ? 'selected' : '' }}>
                                    {{ $empleado->nombre }} ({{ $empleado->cedula }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="limpiarFiltros" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-broom mr-1"></i> Limpiar
                    </button>
                </div>                               
            </div>
        </form>

        <div class="mt-4">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="individual-tab" data-toggle="tab" href="#individual" role="tab">
                        <i class="fas fa-user mr-1"></i> Individuales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab">
                        <i class="fas fa-users mr-1"></i> General
                    </a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white" id="myTabContent">
                <div class="tab-pane fade show active" id="individual" role="tabpanel">
                    <div id="loading-individual" class="text-center py-5" style="display: none;">
                        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando nóminas individuales...</p>
                    </div>
                    <div id="contenido-individual">
                        @if(isset($nominasIndividuales))
                            @if($nominasIndividuales->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Cédula</th>
                                                <th class="text-right">Total Bruto</th>
                                                <th class="text-right">Neto Pagado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($nominasIndividuales as $nomina)
                                                <tr>
                                                    <td>{{ $nomina->empleado->nombre }}</td>
                                                    <td>{{ $nomina->empleado->cedula }}</td>
                                                    <td class="text-right">
                                                        @php
                                                            $factores = [
                                                                1 => 0.5,
                                                                2 => 1,
                                                                3 => 0.25,
                                                            ];
                                                            
                                                            $frecuencia = $nomina->pnomina->tnomina->frecuencia ?? 1;
                                                            $factor = $factores[$frecuencia] ?? 1;
                                                            $totalBruto = $nomina->empleado->salario_base * $factor;
                                                        @endphp
                                                        ${{ number_format($totalBruto, 2) }}
                                                    </td>
                                                    <td class="text-right">${{ number_format($nomina->total_pagado, 2) }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary generar-pdf" 
                                                                data-tipo="individual"
                                                                data-periodo="{{ $nomina->id_pnomina }}"
                                                                data-empleado="{{ $nomina->id_empleado }}">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light text-center mt-3">
                                    @if(request('empleado_id'))
                                        No se encontraron resultados
                                    @else
                                        No hay nóminas para este período
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="alert alert-light text-center mt-3">
                                Seleccione un período para comenzar
                            </div>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="general" role="tabpanel">
                    <div id="loading-general" class="text-center py-5" style="display: none;">
                        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando resumen general...</p>
                    </div>
                    <div id="contenido-general">
                        @if(isset($nominaGeneral))
                            @if($nominaGeneral)
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Resumen General</h5>
                                    <button class="btn btn-success generar-pdf" 
                                            data-tipo="general"
                                            data-periodo="{{ $nominaGeneral['periodo']->id_pnomina }}">
                                        <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-calendar-alt text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-muted small">PERÍODO</h6>
                                                        <h5 class="mb-0">
                                                            {{ $nominaGeneral['periodo']->tnomina->nombre }}
                                                            <small class="text-muted">
                                                                ({{ \Carbon\Carbon::parse($nominaGeneral['periodo']->inicio)->format('d/m/Y') }} - 
                                                                 {{ \Carbon\Carbon::parse($nominaGeneral['periodo']->fin)->format('d/m/Y') }})
                                                            </small>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-users text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-muted small">EMPLEADOS</h6>
                                                        <h5 class="mb-0">{{ $nominaGeneral['total_empleados'] }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-dollar-sign text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-muted small">TOTAL NÓMINA</h6>
                                                        <h5 class="mb-0">${{ number_format($nominaGeneral['total_nomina'], 2) }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light text-center">
                                    Seleccione un período para ver el resumen
                                </div>
                            @endif
                        @else
                            <div class="alert alert-light text-center">
                                Seleccione un período para ver el resumen
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"/>
<style>
    /*
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
    }
    */
    .nav-tabs .nav-link {
        font-weight: 500;
        padding: 12px 20px;
    }
    .btn-block {
        padding: 8px 12px;
    }
    .rounded-circle {
        flex-shrink: 0;
    }
    .table th {
        border-top: none;
    }
    .btn-outline-primary {
        border-width: 2px;
    }
    .spinner-grow {
        opacity: 0.7;
    }
    .text-muted {
        color: #6c757d!important;
    }
    #filtroForm .row {
    display: flex;
    align-items: flex-end;
    }
    #filtroForm select {
        height: 38px
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #d2d6de;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
    #filtroForm .btn {
    height: 38px;
    margin-bottom: 16px;
    }

</style>
@stop

@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.js"></script>
<script>
$(document).ready(function() {
    $('#periodo_id, #empleado_id').select2({
        theme: 'bootstrap',
        language: 'es',
        placeholder: function() {
            $(this).data('placeholder');
        },
        width: '100%'
    });
    $('#filtroForm').on('submit', function(e) {
        e.preventDefault();
        const periodoId = $('#periodo_id').val();
        if(!periodoId) {
            Swal.fire('Error', 'Seleccione un período', 'error');
            return;
        }

        const tabActiva = $('.tab-pane.active').attr('id');
        $(`#loading-${tabActiva}`).show();
        $(`#contenido-${tabActiva}`).hide();

        if(tabActiva === 'individual') {
            cargarNominasIndividuales(periodoId, $('#empleado_id').val());
        } else {
            cargarNominaGeneral(periodoId);
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const periodoId = $('#periodo_id').val();
        if(!periodoId) return;

        const target = $(e.target).attr("href").replace('#', '');
        $(`#loading-${target}`).show();
        $(`#contenido-${target}`).hide();

        if(target === 'individual') {
            cargarNominasIndividuales(periodoId, $('#empleado_id').val());
        } else {
            cargarNominaGeneral(periodoId);
        }
    });

    $('#limpiarFiltros').on('click', function() {
        $('#periodo_id, #empleado_id').val('');
        $('#contenido-individual').html('<div class="alert alert-light text-center mt-3">Seleccione un período para comenzar</div>');
        $('#contenido-general').html('<div class="alert alert-light text-center">Seleccione un período para ver el resumen</div>');
    });

    function cargarNominasIndividuales(periodoId, empleadoId = '') {
        $.ajax({
            url: "{{ route('nempleados.reporte') }}",
            type: 'GET',
            data: { periodo_id: periodoId, empleado_id: empleadoId, tipo: 'individual' },
            success: function(response) {
                $('#contenido-individual').html($(response).find('#contenido-individual').html());
                $('#loading-individual').hide();
                $('#contenido-individual').show();
            },
            error: function() {
                Swal.fire('Error', 'Error al cargar nóminas individuales', 'error');
                $('#loading-individual').hide();
                $('#contenido-individual').show();
            }
        });
    }

    function cargarNominaGeneral(periodoId) {
        $.ajax({
            url: "{{ route('nempleados.reporte') }}",
            type: 'GET',
            data: { periodo_id: periodoId, tipo: 'general' },
            success: function(response) {
                $('#contenido-general').html($(response).find('#contenido-general').html());
                $('#loading-general').hide();
                $('#contenido-general').show();
            },
            error: function() {
                Swal.fire('Error', 'Error al cargar nómina general', 'error');
                $('#loading-general').hide();
                $('#contenido-general').show();
            }
        });
    }

    $(document).on('click', '.generar-pdf', function(e) {
        e.preventDefault();
        const tipo = $(this).data('tipo');
        const periodoId = $(this).data('periodo');
        const url = tipo === 'individual' 
            ? "{{ route('nempleados.pdf', ['periodoId' => ':periodoId', 'empleadoId' => ':empleadoId']) }}"
                .replace(':periodoId', periodoId)
                .replace(':empleadoId', $(this).data('empleado'))
            : "{{ route('nempleados.general', ['periodoId' => ':periodoId']) }}"
                .replace(':periodoId', periodoId);
        window.open(url, '_blank');
    });
});
</script>
@stop