@extends('adminlte::page')

@section('title', 'Editar Devolución')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar Devolución</h1>
        <a href="{{ route('devoluciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('devoluciones.update', $devolucion) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', optional($devolucion->fecha)->format('Y-m-d')) }}" required>
                    @error('fecha')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="monto">Monto</label>
                    <input type="number" name="monto" id="monto" step="0.01" class="form-control" value="{{ old('monto', $devolucion->monto) }}" required>
                    @error('monto')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{ old('descripcion', $devolucion->descripcion) }}" required>
                    @error('descripcion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
    </div>
@stop
