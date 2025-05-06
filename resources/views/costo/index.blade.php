@extends('adminlte::page')

@section('title', 'Costos')

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
                                {{ __('Costos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('costos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
                                    @foreach ($costos as $costo)
                                        <tr>
                                            <td>{{ $costo->id_costos }}</td>
                                            <td>{{ \Carbon\Carbon::parse($costo->f_costos)->format('m/d/Y') }}</td>
                                            <td>{{ $costo->empleado->nombre }}</td>
                                            <td>{{ $costo->descripcion }}</td>
                                            <?php
                                            $subcategorias = [
                                                'compras_insumos' => 'Compras de insumos',
                                                'gasolina' => 'Gasolina',
                                                'mantenimiento_vanes' => 'Mantenimiento a Vanes',
                                                'salario_cerrajero' => 'Salario Cerrajero',
                                                'depreciacion_maquinas' => 'Depreciación de máquinas',
                                                'seguros_vehiculos' => 'Seguros de vehículos',
                                                'alquiler_pulga' => 'Alquiler Pulga',
                                                'codigos' => 'Códigos',
                                                'servicios_subcontratados' => 'Servicios subcontratados',
                                                'costo_extra' => 'Costo Extra',
                                            ];
                                            ?>
                                            <td>{{ $subcategorias[$costo->subcategoria] ?? $costo->subcategoria }}</td>
                                            <td>{{ $costo->valor }}</td>
                                            <td>{{ $costo->estatus }}</td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('costos.destroy', $costo->id_costos) }}" method="POST" class="delete-form" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('costos.show', $costo->id_costos) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('costos.edit', $costo->id_costos) }}">
                                                        <i class="fa fa-fw fa-edit"></i>
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
                {!! $costos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@stop