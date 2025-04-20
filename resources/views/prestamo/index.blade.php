@extends('adminlte::page')

@section('title', 'Prestamos')

@section('content_header')
<h1>Registro de Préstamos por Empleado</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Préstamos por Empleado') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('prestamos.create') }}" class="btn btn-secondary btn-sm float-right" data-placement="left">
                                    {{ __('Crear Nuevo Préstamo') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empleado</th>
                                        <th>Total Préstamos</th>
                                        <th>Préstamos Activos</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $empleadosConPrestamos = $prestamos->groupBy('id_empleado');
                                        $counter = 1;
                                    @endphp
                                    
                                    @foreach ($empleadosConPrestamos as $idEmpleado => $prestamosEmpleado)
                                        @php
                                            $empleado = $prestamosEmpleado->first()->empleado;
                                            $totalPrestamos = $prestamosEmpleado->count();
                                            $prestamosActivos = $prestamosEmpleado->where('activo', 1)->count();
                                        @endphp
                                        <tr>
                                            <td>{{ $empleado->id_empleado }}</td>
                                            <td>{{ $empleado->nombre }}</td>
                                            <td>{{ $totalPrestamos }}</td>
                                            <td>{{ $prestamosActivos }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('prestamos.empleado', $idEmpleado) }}">
                                                    <i class="fa fa-fw fa-eye"></i> Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $prestamos->withQueryString()->links() !!}
            </div>
        </div>
    </div>

    <style>
        .dataTable {
            width: 100% !important;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .dataTable th, .dataTable td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        .dataTable thead th {
            color: black;
            font-weight: bold;
        }
        .dataTable tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05); 
        }
        .btn-sm {
            margin: 2px;
        }
        .dt-buttons .btn {
            margin-right: 5px;
        }
    </style>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
@endpush