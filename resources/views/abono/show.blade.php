@extends('adminlte::page')

@section('title', 'Abono')

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
                            <span class="card-title">{{ __('Abono') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('abonos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>


                    <div class="card-body bg-white">
                        
                        <div class="form-group mb-2 mb20">
                            <strong>ID de Abono:</strong>
                            {{ $abono->id_abonos }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Empleado:</strong>
                            {{ $abono->empleado->nombre }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Concepto:</strong>
                            {{ $abono->concepto }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Valor:</strong>
                            {{ $abono->valor }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Fecha:</strong>
                            {{ $abono->a_fecha }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
