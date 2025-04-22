@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Registro de Ventas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width:100%" id="myTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Fecha</th>
                                    <th>Técnico</th>
                                    <th>Trabajo</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                    <th>Estatus</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registroVs as $registroV)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $registroV->fecha_h->format('d/m/Y') }}</td>
                                    <td>{{ $registroV->tecnico }}</td>
                                    <td>{{ $registroV->trabajo }}</td>
                                    <td>{{ $registroV->cliente }}</td>
                                    <td>{{ number_format($registroV->valor_v, 2) }}</td>
                                    <td>{{ $registroV->estatus }}</td>
                                    <td>{{ $registroV->descripcion_ce }}</td>
                                    <td>
                                        <a href="{{ route('registro-vs.show', $registroV->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('registro-vs.edit', $registroV->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar registro?')">
                                                <i class="fas fa-trash"></i>
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
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    });
</script>
@stop