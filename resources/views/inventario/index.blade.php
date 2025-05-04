@extends('adminlte::page')

@section('title', 'Inventario')

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
                            {{ __('Inventario') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('inventarios.export') }}" class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </a>
                            <a href="{{ route('inventarios.create') }}" class="btn btn-secondary btn-sm float-right" data-placement="left">
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
                                    <th class="text-center align-middle">ID Inventario</th>
                                    <th class="text-center align-middle">ID Producto</th>
                                    <th class="text-center align-middle">Producto</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Almacén</th>
                                    <th class="text-center align-middle">Total $</th>
                                    <th class="text-center align-middle">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventarios1 as $inventario)
                                <tr>
                                    <td class="text-center align-middle">{{ $inventario->id_inventario}}</td>
                                    <td class="text-center align-middle">{{ $inventario->producto->id_producto}}</td>
                                    <td class="text-center align-middle">{{ $inventario->producto->item }}</td>
                                    <td class="text-center align-middle">
                                        <span class="
                                            @if($inventario->cantidad <= 5) bg-danger text-white p-1 rounded
                                            @elseif($inventario->cantidad <= 10) bg-warning text-dark p-1 rounded
                                            @else bg-success text-white p-1 rounded
                                            @endif">
                                            {{ $inventario->cantidad }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">{{ $inventario->almacene->nombre }}</td>
                                    <td class="text-center align-middle">$ {{ number_format($inventario->cantidad * $inventario->producto->precio, 2) }}</td>

                                    <td class="text-center align-middle">
                                        <form onsubmit="return confirmDelete(this)" action="{{ route('inventarios.destroy', $inventario->id_inventario) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                            <a class="btn btn-sm btn-primary" href="{{ route('inventarios.show', $inventario->id_inventario) }}">
                                                <i class="fa fa-fw fa-eye"></i> 
                                            </a>
                                            <a class="btn btn-sm btn-success" href="{{ route('inventarios.edit', $inventario->id_inventario) }}">
                                            <i class="fas fa-exchange-alt"></i>
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
<style>
    .btn-export-excel {
    background-color: #1d6f42;
    color: white;
    border-color: #1a633b;
}

.btn-export-excel:hover {
    background-color: #1a633b;
    color: white;
}
</style>

@stop
