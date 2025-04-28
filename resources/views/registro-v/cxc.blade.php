@extends('adminlte::page')

@section('title', 'Cuentas por Cobrar')

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
                                {{ __('Cuentas por Cobrar') }}
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
                                        <th>Lugar de Venta</th>
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
                                            <td>{{ $registroV->lugarventa }}</td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)"  action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" class="delete-form" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('registro-vs.show', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('registro-vs.edit', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    <a href="{{ route('registro-vs.pdf', $registroV->id) }}" class="btn btn-sm btn-warning" target="_blank">
                                                        <i class="fa fa-fw fa-print"></i> 
                                                        Es
                                                    </a>
                                                    <a href="{{ route('invoice.pdf', $registroV->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fa fa-fw fa-print"></i>
                                                        En
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"> 
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
@stop