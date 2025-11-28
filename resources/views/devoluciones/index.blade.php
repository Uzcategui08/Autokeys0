@extends('adminlte::page')

@section('title', 'Devoluciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Devoluciones</h1>
        <a href="{{ route('devoluciones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar devolución
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devoluciones as $devolucion)
                        <tr>
                            <td>{{ $devolucion->fecha->format('d/m/Y') }}</td>
                            <td>${{ number_format($devolucion->monto, 2) }}</td>
                            <td>{{ $devolucion->descripcion }}</td>
                            <td class="text-right">
                                <a href="{{ route('devoluciones.edit', $devolucion) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('devoluciones.destroy', $devolucion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta devolución?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay devoluciones registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $devoluciones->links() }}
        </div>
    </div>
@stop
