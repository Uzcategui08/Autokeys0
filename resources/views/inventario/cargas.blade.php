@extends('adminlte::page')

@section('title', 'Cargas/Descargas')

@section('content_header')
<h1>Registro de Ajustes de Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Cargas/Descargas') }}
                        </span>
                    </div>
                </div>

                <div class="card-body bg-white">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable">
                            <thead class="thead">
                                <tr>
                                    <th class="text-center align-middle">Usuario</th>
                                    <th class="text-center align-middle">ID Producto</th>
                                    <th class="text-center align-middle">Producto</th>
                                    <th class="text-center align-middle">Almacén</th>
                                    <th class="text-center align-middle">Tipo</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Cantidad anterior</th>
                                    <th class="text-center align-middle">Cantidad nueva</th>
                                    <th class="text-center align-middle">Motivo</th>
                                    <th class="text-center align-middle">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cargas as $ajuste)
                                <tr>
                                    <td class="text-center align-middle">{{ $ajuste->user->name}}</td>
                                    <td class="text-center align-middle">{{ $ajuste->producto->id_producto }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->producto->item }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->almacene->nombre }}</td>
                                    <td class="text-center align-middle">
                                        @if($ajuste->tipo == 'carga')
                                            <span class="badge bg-success">Carga</span>
                                        @else
                                            <span class="badge bg-danger">Descarga</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">{{ $ajuste->diferencia }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->cantidad_anterior }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->cantidad_nueva }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->descripcion }}</td>
                                    <td class="text-center align-middle">{{ $ajuste->created_at->format('m/d/Y') }}</td>
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
