<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_pnomina" class="form-label">{{ __('ID Período de Nómina') }}</label>
                    <input type="text" name="id_pnomina" class="form-control @error('id_pnomina') is-invalid @enderror" value="{{ old('id_pnomina', $pnomina?->id_pnomina) }}" id="id_pnomina" placeholder="ID Período de Nómina">
                    {!! $errors->first('id_pnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="id_tnomina" class="form-label">{{ __('Tipo de Nómina') }}</label>
                    <select name="id_tnomina" id="id_tnomina" class="form-control @error('id_tnomina') is-invalid @enderror" onchange="cargarEmpleados(this.value)">
                        <option value="">{{ __('Seleccione un tipo de nómina') }}</option>
                        @foreach($tnominas as $tnomina)
                            <option value="{{ $tnomina->id_tnomina }}" {{ old('id_tnomina', $pnomina?->id_tnomina) == $tnomina->id_tnomina ? 'selected' : '' }}>
                                {{ $tnomina->nombre }} - {{ [
                                    1 => 'Quincenal',
                                    2 => 'Mensual',
                                    3 => 'Semanal',
                                ][$tnomina->frecuencia] ?? 'Frecuencia desconocida' }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_tnomina', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>            
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="inicio" class="form-label">{{ __('Inicio') }}</label>
                    <input type="date" name="inicio" class="form-control @error('inicio') is-invalid @enderror" 
                           value="{{ old('inicio', isset($pnomina->inicio) ? $pnomina->inicio->format('Y-m-d') : '') }}" 
                           id="inicio" required>
                    {!! $errors->first('inicio', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2 mb20">
                    <label for="fin" class="form-label">{{ __('Fin') }}</label>
                    <input type="date" name="fin" class="form-control @error('fin') is-invalid @enderror" 
                           value="{{ old('fin', isset($pnomina->fin) ? $pnomina->fin->format('Y-m-d') : '') }}" 
                           id="fin" required>
                    {!! $errors->first('fin', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                </div>
            </div>          
        </div>

        <div class="row mt-4" id="empleados-container"> <!-- Quitamos el style="display: none;" -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Empleados de esta nómina</h4>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" onclick="recargarEmpleados()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="loading-empleados" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando empleados...</p>
                        </div>
                        <div class="table-responsive" id="tabla-empleados">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cédula</th>
                                        <th>Salario Base</th>
                                        <th>Método de Pago</th>
                                    </tr>
                                </thead>
                                <tbody id="empleados-body">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Seleccione un tipo de nómina para cargar los empleados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .card-title {
        margin-bottom: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .text-muted.py-4 {
        padding: 1.5rem 0;
        font-style: italic;
        color: #6c757d;
    }
</style>
</style>

<script>
function cargarEmpleados(id_tnomina) {
    const empleadosContainer = document.getElementById('empleados-container');
    const loadingElement = document.getElementById('loading-empleados');
    const tablaElement = document.getElementById('tabla-empleados');
    const tbody = document.getElementById('empleados-body');

    loadingElement.style.display = 'block';
    tablaElement.style.display = 'none';

    tbody.innerHTML = '';

    if (!id_tnomina) {
        loadingElement.style.display = 'none';
        tablaElement.style.display = 'block';
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    Seleccione un tipo de nómina para cargar los empleados
                </td>
            </tr>
        `;
        return;
    }
    
    fetch(`/empleados-por-tnomina/${id_tnomina}`)
        .then(response => response.json())
        .then(data => {
            if(data.empleados.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No hay empleados asignados a esta nómina
                        </td>
                    </tr>
                `;
            } else {
                data.empleados.forEach(empleado => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${empleado.nombre}</td>
                        <td>${empleado.cedula}</td>
                        <td>${formatCurrency(empleado.salario_base)}</td>
                        <td>
                            <select name="metodos_pago[${empleado.id_empleado}]" class="form-control">
                                <option value="1">Transferencia Bancaria</option>
                                <option value="2">Cheque</option>
                                <option value="3">Efectivo</option>
                                <option value="4">Otro</option>
                            </select>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger py-4">
                        Error al cargar los empleados. Intente nuevamente.
                    </td>
                </tr>
            `;
        })
        .finally(() => {
            loadingElement.style.display = 'none';
            tablaElement.style.display = 'block';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const id_tnomina = document.getElementById('id_tnomina').value;
    cargarEmpleados(id_tnomina); 
});

function recargarEmpleados() {
    const id_tnomina = document.getElementById('id_tnomina').value;
    if (id_tnomina) {
        cargarEmpleados(id_tnomina);
    }
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>