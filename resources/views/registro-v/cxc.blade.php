@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Registro de Ventas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <span id="card_title">
                            {{ __('Registro Cuentas por Cobrar') }}
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
                                    <th>Tecnico</th>
                                    <th>Trabajo</th>
                                    <th>Cliente</th>
                                    <th>Valor V</th>
                                    <th>Estatus</th>
                                    <th>Descripcion Ce</th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registroVs as $registroV)
                                <tr>
                                    <td>{{ ++$i }}</td>

                                    <td>{{ $registroV->fecha_h->format('m/d/Y') }}</td>
                                    <td>{{ $registroV->tecnico }}</td>
                                    <td>{{ $registroV->trabajo }}</td>
                                    <td>{{ $registroV->cliente}}</td>
                                    <td>{{ $registroV->valor_v }}</td>
                                    <td>{{ $registroV->estatus }}</td>
                                    <td>{{ $registroV->descripcion_ce }}</td>
                                    <td>
                                        <form action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST">
                                            <a class="btn btn-sm btn-primary " href="{{ route('registro-vs.show', $registroV->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                            <a class="btn btn-sm btn-success" href="{{ route('registro-vs.edit', $registroV->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
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

@section('js')
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
@stop