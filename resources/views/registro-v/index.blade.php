
@extends('adminlte::page')

@section('title', 'Ventas')

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
                                {{ __('Ventas') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('registro-vs.create') }}" class="btn btn-secondary btn-m float-right" data-placement="left">
                                    {{ __('Crear Nuevo') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>ID Venta</th>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Vehículo</th>
                                        <th>Cliente</th>
                                        <th>Valor de Venta</th>
                                        <th>% Técnico</th>
                                        <th>Estatus</th>
                                        <th>Lugar de Venta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registroVs as $registroV)
                                        <tr>
                                            <td>{{ $registroV->id }}</td>
                                            <td>{{ $registroV->fecha_h->format('m/d/Y') }}</td>
                                            <td>{{ $registroV->empleado->nombre }}</td>
                                            <td>{{ $registroV->modelo }}</td>
                                            <td>{{ $registroV->cliente }}</td>
                                            <td>{{ $registroV->valor_v }}</td>
                                            <td>{{ $registroV->porcentaje_c }}</td>
                                            @php
                                                $estatusFormateados = [
                                                    'pagado' => 'Pagado',
                                                    'pendiente' => 'Pendiente',
                                                    'parcialemente pagado' => 'Parcialmente Pagado',
                                                ];
                                            @endphp
                                            <td>{{ $estatusFormateados[$registroV->estatus] ?? strtoupper($registroV->estatus) }}</td>
                                            <td>{{ $registroV->lugarventa }}</td>
                                            <td>
                                                <div style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('registro-vs.show', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('registro-vs.edit', $registroV->id) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    <a href="{{ route('registro-vs.pdf', $registroV->id) }}" class="btn btn-sm btn-warning" target="_blank">
                                                        <i class="">Es</i>
                                                    </a>
                                                    <a href="{{ route('invoice.pdf', $registroV->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="En">En</i>
                                                    </a>
                                                    <form onsubmit="return confirmDelete(this)" action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"> 
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