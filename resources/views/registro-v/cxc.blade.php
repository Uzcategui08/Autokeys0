@extends('adminlte::page')

@section('title', 'Cuentas por Cobrar')

@section('content_header')
<br>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Registro de Cuentas por Cobrar') }}
                            </span>

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
                                        <th>No</th>
                                        <th>Fecha H</th>
                                        <th>Técnico</th>
                                        <th>Trabajo</th>
                                        <th>Vehiculo</th>
                                        <th>Cliente</th>
                                        <th>Valor Venta</th>
                                        <th>% Técnico</th>
                                        <th>Estatus</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registroVs as $registroV)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $registroV->fecha_h->format('m/d/Y') }}</td>
                                            <td>{{ $registroV->tecnico }}</td>
                                            <td>{{ $registroV->trabajo }}</td>
                                            <td>{{ $registroV->modelo }}</td>
                                            <td>{{ $registroV->cliente }}</td>
                                            <td>{{ $registroV->valor_v }}</td>
                                            <td>{{ $registroV->porcentaje_c }}</td>
                                            <td>{{ $registroV->estatus }}</td>
                                            <td>{{ $registroV->descripcion_ce }}</td>
                                            <td>
                                                <form action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('registro-vs.show', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('registro-vs.edit', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Estás seguro de eliminar?') ? this.closest('form').submit() : false;">
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
                {!! $registroVs->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
    <style>
        .dataTable {
            width: 100% !important;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .dataTable th,
        .dataTable td {
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .dataTable thead th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            position: sticky;
            top: 0;
        }

        .dataTable tbody tr {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 8px;
        }

        .dataTable tbody tr:hover {
            background-color: #f5f5f5;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            min-width: 70px;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
            padding: 15px 20px;
        }

        .alert {
            margin: 15px;
            border-radius: 5px;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
   
            });
        });
    </script>
@stop