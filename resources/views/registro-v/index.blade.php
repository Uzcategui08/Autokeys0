@extends('adminlte::page')

@section('title', 'Ventas')

@section('content_header')
    <h1>Registro de Ventas</h1>
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
                                        <th>Cliente</th>
                                        <th>Tipo de Trabajo</th>
                                        <th>Métodos de Pago</th>
                                        <th>Titular</th>
                                        <th>Productos</th>
                                        <th>Valor</th>
                                        <th>Comisión</th>
                                        <th>Cargado</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registroVs as $registroV)
                                        @php
                                            $items = is_array($registroV->items) ? $registroV->items : (json_decode($registroV->items, true) ?? []);
                                            $pagos = is_array($registroV->pagos) ? $registroV->pagos : (json_decode($registroV->pagos, true) ?? []);

                                            $trabajos = collect($items)->pluck('trabajo')->filter()->unique()->toArray();

                                            $metodosPago = [];
                                            foreach ($pagos as $pago) {
                                                $metodo = $pago['metodo_pago'] ?? '';
                                                $monto = isset($pago['monto']) ? number_format($pago['monto'], 2) : '0.00';
                                                if (is_numeric($metodo)) {
                                                    $nombreMetodo = $tiposDePago[$metodo]->name ?? $metodo;
                                                    $metodosPago[] = "$nombreMetodo (\$$monto)";
                                                } else {
                                                    $metodosPago[] = "$metodo (\$$monto)";
                                                }
                                            }

                                            $productosArr = [];
                                            foreach ($items as $item) {
                                                if (isset($item['productos']) && is_array($item['productos'])) {
                                                    foreach ($item['productos'] as $producto) {
                                                        $nombre = $producto['nombre_producto'] ?? 'Producto no especificado';
                                                        $cantidad = $producto['cantidad'] ?? 0;
                                                        $codigo = $productos[$nombre]->id_producto ?? 'N/A';
                                                        $productosArr[] = [
                                                            'nombre' => $nombre,
                                                            'codigo' => $codigo,
                                                            'cantidad' => $cantidad
                                                        ];
                                                    }
                                                }
                                            }

                                            $cargado = (int)($registroV->cargado ?? 0);
                                            $cargadoClass = $cargado === 1 ? 'bg-success' : 'bg-secondary';
                                            $cargadoIcon = $cargado === 1 ? 'fa-check-circle' : 'fa-times-circle';
                                            $cargadoText = $cargado === 1 ? 'Sí' : 'No';

                                            $estadosStyles = [
                                                'pagado' => ['class' => 'badge-success', 'icon' => 'fa-check-circle'],
                                                'pendiente' => ['class' => 'badge-danger', 'icon' => 'fa-clock'],
                                                'parcialemente pagado' => ['class' => 'badge-warning', 'icon' => 'fa-money-bill-wave']
                                            ];
                                            $estado = $estadosStyles[strtolower($registroV->estatus)] ?? ['class' => 'badge-secondary', 'icon' => 'fa-question'];
                                        @endphp
                                        <tr class="{{ $cargado === 1 ? 'table-success' : '' }}" data-cargado="{{ $cargado }}">
                                            <td class="font-weight-bold">{{ $registroV->id }}</td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="far fa-calendar-alt text-primary mr-1"></i>
                                                    {{ $registroV->fecha_h->format('m/d/Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="fas fa-user-tie text-info mr-1"></i>
                                                    {{ $registroV->empleado->nombre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                                    <i class="fas fa-user mr-1 text-secondary"></i>
                                                    {{ $registroV->cliente }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($trabajos))
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($trabajos, 0, 2) as $trabajo)
                                                            <span class="badge bg-info text-white">{{ $trabajo }}</span>
                                                        @endforeach
                                                        @if(count($trabajos) > 2)
                                                            <span class="badge bg-light text-dark">+{{ count($trabajos) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($metodosPago))
                                                    <div class="d-flex flex-column">
                                                        @foreach(array_slice($metodosPago, 0, 2) as $metodo)
                                                            <small class="text-nowrap">
                                                                <i class="fas fa-credit-card mr-1 text-primary"></i>
                                                                {{ $metodo }}
                                                            </small>
                                                        @endforeach
                                                        @if(count($metodosPago) > 2)
                                                            <small class="text-muted">+{{ count($metodosPago) - 2 }} más</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-nowrap">
                                                    <i class="fas fa-id-card text-secondary mr-1"></i>
                                                    {{ $registroV->titular_c ?: 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($productosArr))
                                                    <div class="d-flex flex-column">
                                                        @foreach(array_slice($productosArr, 0, 2) as $producto)
                                                            <small>
                                                                <span class="font-weight-bold">#{{ $producto['codigo'] }}</span> - 
                                                                {{ $producto['nombre'] }}
                                                                <span class="text-muted">x{{ $producto['cantidad'] }}</span>
                                                            </small>
                                                        @endforeach
                                                        @if(count($productosArr) > 2)
                                                            <small class="text-muted">+{{ count($productosArr) - 2 }} más</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-success">
                                                <i class="fas fa-dollar-sign mr-1"></i>
                                                {{ number_format($registroV->valor_v, 2) }}
                                            </td>
                                            <td class="font-weight-bold text-primary">
                                                <i class="fas fa-percentage mr-1"></i>
                                                {{ number_format($registroV->porcentaje_c, 2) }}
                                            </td>
                                            <td>
                                                <form class="toggle-cargado-form" action="{{ route('registro-vs.toggle-cargado', $registroV->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm toggle-cargado-btn {{ $cargadoClass }}" data-cargado="{{ $cargado }}">
                                                        <i class="fas {{ $cargadoIcon }}"></i> {{ $cargadoText }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <span class="badge {{ $estado['class'] }}">
                                                    <i class="fas {{ $estado['icon'] }} mr-1"></i>
                                                    {{ ucfirst($registroV->estatus) }}
                                                </span>
                                            </td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('registro-vs.destroy', $registroV->id) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
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


@stop

@section('css')
    <style>
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        .table td {
            vertical-align: middle;
        }
        .text-nowrap {
            white-space: nowrap;
        }
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .toggle-cargado-btn {
            width: 80px;
            color: white;
            transition: all 0.3s ease;
        }
        .toggle-cargado-btn:hover {
            opacity: 0.8;
        }
        .table-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
        .table tr {
            transition: background-color 0.3s ease;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            function showToggleAlert(button, newValue) {
                const title = newValue ? '¿Marcar como cargado?' : '¿Marcar como no cargado?';
                const text = newValue ? 'La venta aparecerá como completada en el sistema.' : 'La venta volverá a estado pendiente.';
                
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleCargado(button, newValue);
                    }
                });
            }

            function toggleCargado(button, newValue) {
                const form = button.closest('form');
                const row = button.closest('tr');
                // Enviar PATCH correctamente
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize() + '&_method=PATCH',
                    success: function(response) {
                        if (typeof response.cargado !== 'undefined') {
                            const cargadoReal = response.cargado == 1 || response.cargado === true;
                            if (cargadoReal) {
                                button.removeClass('bg-secondary').addClass('bg-success');
                                button.html('<i class="fas fa-check-circle"></i> Sí');
                                row.addClass('table-success').attr('data-cargado', '1');
                                button.data('cargado', 1);
                            } else {
                                button.removeClass('bg-success').addClass('bg-secondary');
                                button.html('<i class="fas fa-times-circle"></i> No');
                                row.removeClass('table-success').attr('data-cargado', '0');
                                button.data('cargado', 0);
                            }
                            Swal.fire(
                                '¡Actualizado!',
                                'El estado ha sido cambiado.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error',
                                'No se pudo actualizar el estado',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        Swal.fire(
                            'Error',
                            'No se pudo actualizar el estado',
                            'error'
                        );
                    }
                });
            }

            $(document).on('click', '.toggle-cargado-btn', function(e) {
                e.preventDefault(); // Evita el submit del form
                const button = $(this);
                const currentValue = button.data('cargado') == 1;
                showToggleAlert(button, !currentValue);
            });

        });
    </script>
@stop