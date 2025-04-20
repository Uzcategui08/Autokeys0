@extends('adminlte::page')

@section('title', 'Abonos')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Abonos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('abonos.create') }}" class="btn btn-secondary btn-sm float-right"  data-placement="left">
                                  {{ __('Crear Nuevo Abono') }}
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

									<th >ID de Abono</th>
									<th >Empleado</th>
									<th >Concepto</th>
									<th >Valor</th>
									<th >Fecha</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($abonos as $abono)
                                        <tr>
                                                                                    
										<td >{{ $abono->id_abonos }}</td>
										<td >{{ $abono->empleado->nombre}}</td>
										<td >{{ $abono->concepto }}</td>
										<td >{{ $abono->valor }}</td>
                                        <td>{{ \Carbon\Carbon::parse($abono->a_fecha)->format('m/d/Y') }}</td>

                                            <td>
                                                <form action="{{ route('abonos.destroy', $abono->id_abonos) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('abonos.show', $abono->id_abonos) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('abonos.edit', $abono->id_abonos) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $abonos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
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


