@extends('adminlte::page')

@section('title', 'Empleado')

@section('content_header')
<h1>Detalle</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Empleado') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('empleados.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Empleado:</strong>
                                    {{ $empleado->id_empleado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $empleado->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cedula:</strong>
                                    {{ $empleado->cedula }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cargo:</strong>
                                    {{ $empleado->cargo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Salario Base:</strong>
                                    {{ $empleado->salario_base }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Metodo Pago:</strong>
                                    {{ $empleado->metodo_pago }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
