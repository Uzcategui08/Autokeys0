@extends('adminlte::page')

@section('title', 'Presupuestos')

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
                            <span class="card-title">{{ __('Tipo de Nómina') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('tnominas.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                        <div class="form-group mb-2 mb20">
                            <strong>ID de Nómina:</strong>
                            {{ $tnomina->id_tnomina }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Nombre:</strong>
                            {{ $tnomina->nombre }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Frecuencia:</strong>
                            @if($tnomina->frecuencia == 1)
                                Quincenal
                            @elseif($tnomina->frecuencia == 2)
                                Mensual
                            @elseif($tnomina->frecuencia == 3)
                                Semanal
                            @else
                                Frecuencia desconocida
                            @endif
                        </div>
                                
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
