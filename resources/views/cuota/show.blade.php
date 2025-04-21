@extends('layouts.app')

@section('template_title')
    {{ $cuota->name ?? __('Show') . " " . __('Cuota') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Cuota</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('cuotas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Cuotas:</strong>
                                    {{ $cuota->id_cuotas }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Prestamos:</strong>
                                    {{ $cuota->id_prestamos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Valor:</strong>
                                    {{ $cuota->valor }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Vencimiento:</strong>
                                    {{ $cuota->fecha_vencimiento }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Pagada:</strong>
                                    {{ $cuota->pagada }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
