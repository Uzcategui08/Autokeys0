@extends('layouts.app')

@section('template_title')
    {{ $registroV->name ?? __('Show') . " " . __('Registro V') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Registro V</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('registro-vs.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha H:</strong>
                                    {{ $registroV->fecha_h }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tecnico:</strong>
                                    {{ $registroV->tecnico }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Trabajo:</strong>
                                    {{ $registroV->trabajo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cliente:</strong>
                                    {{ $registroV->cliente }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Telefono:</strong>
                                    {{ $registroV->telefono }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Valor V:</strong>
                                    {{ $registroV->valor_v }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estatus:</strong>
                                    {{ $registroV->estatus }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Metodo P:</strong>
                                    {{ $registroV->metodo_p }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Titular C:</strong>
                                    {{ $registroV->titular_c }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cobro:</strong>
                                    {{ $registroV->cobro }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion Ce:</strong>
                                    {{ $registroV->descripcion_ce }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Monto Ce:</strong>
                                    {{ $registroV->monto_ce }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Metodo Pce:</strong>
                                    {{ $registroV->metodo_pce }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Porcentaje C:</strong>
                                    {{ $registroV->porcentaje_c }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Marca:</strong>
                                    {{ $registroV->marca }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Modelo:</strong>
                                    {{ $registroV->modelo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Año:</strong>
                                    {{ $registroV->año }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Items:</strong>
                                    {{ $registroV->items }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
