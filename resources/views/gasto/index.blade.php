@extends('adminlte::page')

@section('title', 'Gastos')

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
                                {{ __('Gastos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('gastos.create') }}" class="btn btn-secondary btn-sm float-right"  data-placement="left">
                                  {{ __('Crear Nuevo Gasto') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Descripción</th>
                                        <th>Subcategoría</th>
                                        <th>Valor</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gastos as $gasto)
                                        <tr>
                                            <td>{{ $gasto->id_gastos }}</td>
                                            <td>{{ \Carbon\Carbon::parse($gasto->f_gastos)->format('m/d/Y') }}</td>
                                            <td>{{ $gasto->id_tecnico }}</td>
                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>{{ $gasto->subcategoria }}</td>
                                            <td>{{ $gasto->valor }}</td>
                                            <td>{{ $gasto->estatus }}</td>
                                            <td>
                                                <form action="{{ route('gastos.destroy', $gasto->id_gastos) }}" method="POST" class="delete-form">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('gastos.show', $gasto->id_gastos) }}">
                                                        <i class="fa fa-fw fa-eye"></i> {{ __('Show') }}
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('gastos.edit', $gasto->id_gastos) }}">
                                                        <i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
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
                {!! $gastos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.4.24/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": 5000
        };

        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        $(document).ready(function() {
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrá revertir esta acción!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@stop


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