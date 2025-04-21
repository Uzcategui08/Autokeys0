@extends('adminlte::page')

@section('title', 'Préstamos de ' . $empleado->nombre)

@section('content_header')
    <h1>Préstamos de {{ $empleado->nombre }}</h1>
    <a href="{{ route('prestamos.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Volver a la lista general
    </a>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                Detalle de Préstamos
                            </span>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>ID Préstamo</th>
                                        <th>Valor</th>
                                        <th>Cuotas</th>
                                        <th>Cuota Actual</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prestamos as $i => $prestamo)
                                        <tr>
                                            <td>{{ $prestamo->id_prestamos }}</td>
                                            <td>${{ number_format($prestamo->valor, 2) }}</td>
                                            <td>{{ $prestamo->cuotas }}</td>
                                            <td>{{ $prestamo->cuota_actual }}</td>
                                            <td>
                                                @if($prestamo->activo)
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary">Completado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('prestamos.cuotas', $prestamo->id_prestamos) }}">
                                                    <i class="fa fa-fw fa-eye"></i> Ver
                                                </a>
                                                <a class="btn btn-sm btn-success" href="{{ route('prestamos.edit', $prestamo->id_prestamos) }}">
                                                    <i class="fa fa-fw fa-edit"></i> Editar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $prestamos->links() !!}
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
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
@endpush

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