@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1>Inventario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <span id="card_title">
                            {{ __('Inventarios') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('inventarios.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Crear Inventario') }}
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
                                    <th class="text-center align-middle">ID Inventario</th>
                                    <th class="text-center align-middle">ID Producto</th>
                                    <th class="text-center align-middle">Producto</th>
                                    <th class="text-center align-middle">Almacén</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Total $</th>
                                    <th class="text-center align-middle">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventarios1 as $inventario)
                                <tr>
                                    <td class="text-center align-middle">{{ $inventario->id_inventario }}</td>
                                    <td class="text-center align-middle">{{ $inventario->producto->id_producto}}</td>
                                    <td class="text-center align-middle">{{ $inventario->producto->item }}</td>
                                    <td class="text-center align-middle">{{ $inventario->almacene->nombre }}</td>
                                    <td class="text-center align-middle">{{ $inventario->cantidad }}</td>
                                    <td class="text-center align-middle">$ {{ number_format($inventario->cantidad * $inventario->producto->precio, 2) }}</td>

                                    <td class="text-center align-middle">
                                        <form action="{{ route('inventarios.destroy', $inventario->id_inventario) }}" method="POST">
                                            <a class="btn btn-sm btn-primary" href="{{ route('inventarios.show', $inventario->id_inventario) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                            </a>
                                            <a class="btn btn-sm btn-success" href="{{ route('inventarios.edit', $inventario->id_inventario) }}">
                                                <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                            </a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;">
                                                <i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}
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
            {!! $inventarios->withQueryString()->links() !!}
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