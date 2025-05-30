@extends('adminlte::page')

@section('title', 'Nómina')

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
                                {{ __('Nómina de Empleados') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('nempleados.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
                                        <th>ID Nómina</th>
                                        <th>Fecha de Pago</th>
                                        <th>ID Empleado</th>
                                        <th>Total Descuentos</th>
                                        <th>Total Abonos</th>
                                        <th>Total Pagado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nempleados as $nempleado)
                                        <tr>
                                            <td>{{ $nempleado->id_nempleado }}</td>
                                            <td>{{ \Carbon\Carbon::parse($nempleado->fecha_pago)->format('d/m/Y') }}</td>
                                            <td>{{ $nempleado->empleado->nombre ?? 'N/A' }}</td>
                                            <td>${{ number_format($nempleado->total_descuentos, 2) }}</td>
                                            <td>${{ number_format($nempleado->total_abonos, 2) }}</td>
                                            <td>${{ number_format($nempleado->total_pagado, 2) }}</td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('nempleados.destroy', $nempleado->id_nempleado) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
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
@endsection