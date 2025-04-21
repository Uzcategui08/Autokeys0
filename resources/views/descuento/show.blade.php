@extends('adminlte::page')

@section('title', 'Descuento')

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
                            <span class="card-title">{{ __('Descuento') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('descuentos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>ID Descuento:</strong>
                                    {{ $descuento->id_descuentos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Empleado:</strong>
                                    {{ $descuento->empleado->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Concepto:</strong>
                                    {{ $descuento->concepto }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Valor:</strong>
                                    {{ $descuento->valor }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha:</strong>
                                    {{ $descuento->d_fecha }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
