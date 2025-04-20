@extends('adminlte::page')

@section('title', 'Período de Nómina')

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
                            <span class="card-title">{{ __('Período de Nómina') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('pnominas.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>ID de Período de Nómina:</strong>
                                    {{ $pnomina->id_pnomina }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo de Nómina:</strong>
                                    {{ $pnomina->tnomina->nombre }} - {{ [
                                        1 => 'Quincenal',
                                        2 => 'Mensual',
                                        3 => 'Semanal',
                                    ][$pnomina->tnomina->frecuencia] ?? 'Frecuencia desconocida' }}
                                </div>
                                
                                <div class="form-group mb-2 mb20">
                                    <strong>Inicio:</strong>
                                    {{ \Carbon\Carbon::parse($pnomina->inicio)->format('m/d/Y') }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fin:</strong>
                                    {{ \Carbon\Carbon::parse($pnomina->fin)->format('m/d/Y') }}
                                </div>                               
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
