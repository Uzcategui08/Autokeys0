@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Registro de Presupuestos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Presupuestos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('presupuestos.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Crear Presupuesto') }}
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
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Validez</th>
                                        <th>Descuento</th>
                                        <th>IVA</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($presupuestos as $presupuesto)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $presupuesto->id_presupuesto }}</td>
                                            <td>{{ $presupuesto->cliente->nombre }}</td>
                                            <td>{{ $presupuesto->f_presupuesto }}</td>
                                            <td>{{ $presupuesto->validez }}</td>
                                            <td>{{ $presupuesto->descuento }}%</td>
                                            <td>{{ $presupuesto->iva }}%</td>
                                            <td>
                                                <span class="badge badge-lg fs-6 p-2
                                                            @if($presupuesto->estado == 'aprobado') badge-success 
                                                            @elseif($presupuesto->estado == 'pendiente') badge-warning 
                                                            @elseif($presupuesto->estado == 'rechazado') badge-danger 
                                                            @else badge-secondary 
                                                            @endif">
                                                    {{ $presupuesto->estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('presupuestos.destroy', $presupuesto->id_presupuesto) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('presupuestos.show', $presupuesto->id_presupuesto) }}">
                                                        <i class="fa fa-fw fa-eye"></i> 
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('presupuestos.edit', $presupuesto->id_presupuesto) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('presupuestos.pdf', $presupuesto->id_presupuesto) }}" target="_blank">
                                                        <i class="fa fa-fw fa-print"></i> 
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Estás seguro de eliminar este presupuesto?') ? this.closest('form').submit() : false;">
                                                        <i class="fa fa-fw fa-trash"></i> 
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
    <style>
         
        .dataTable {
            width: 100% !important;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .dataTable th,
        .dataTable td {
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
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
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