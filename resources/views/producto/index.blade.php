@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Productos</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">

                        <span id="card_title">
                            {{ __('Productos') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Crear Producto') }}
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
                                    <th>No</th>

                                    <th>Id Producto</th>
                                    <th>Item</th>
                                    <th>Marca</th>
                                    <th>T Llave</th>
                                    <th>Sku</th>
                                    <th>Precio</th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                <tr>
                                    <td>{{ ++$i }}</td>

                                    <td>{{ $producto->id_producto }}</td>
                                    <td>{{ $producto->item }}</td>
                                    <td>{{ $producto->marca }}</td>
                                    <td>{{ $producto->t_llave }}</td>
                                    <td>{{ $producto->sku }}</td>
                                    <td>{{ $producto->precio }}</td>

                                    <td>
                                        <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST">
                                            <a class="btn btn-sm btn-primary " href="{{ route('productos.show', $producto->id_producto) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                            <a class="btn btn-sm btn-success" href="{{ route('productos.edit', $producto->id_producto) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
            {!! $productos->withQueryString()->links() !!}
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