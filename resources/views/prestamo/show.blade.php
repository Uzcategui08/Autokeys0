@extends('layouts.app')

@section('template_title')
    {{ $prestamo->name ?? __('Show') . " " . __('Prestamo') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Prestamo</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('prestamos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Prestamos:</strong>
                                    {{ $prestamo->id_prestamos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Empleado:</strong>
                                    {{ $prestamo->id_empleado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Valor:</strong>
                                    {{ $prestamo->valor }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cuotas:</strong>
                                    {{ $prestamo->cuotas }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Cuota Actual:</strong>
                                    {{ $prestamo->cuota_actual }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Activo:</strong>
                                    {{ $prestamo->activo }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
