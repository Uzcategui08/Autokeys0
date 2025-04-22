@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<section class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="float-left">
                        <span class="card-title">{{ __('Show') }} Inventario</span>
                    </div>
                    <div class="float-right">
                        <a class="btn btn-primary btn-sm" href="{{ route('inventarios.index') }}"> {{ __('Back') }}</a>
                    </div>
                </div>

                <div class="card-body bg-white">

                    <div class="form-group mb-2 mb20">
                        <strong>Id Inventario:</strong>
                        {{ $inventario->id_inventario }}
                    </div>
                    <div class="form-group mb-2 mb20">
                        <strong>Id Producto:</strong>
                        {{ $inventario->id_producto }}
                    </div>
                    <div class="form-group mb-2 mb20">
                        <strong>Id Almacen:</strong>
                        {{ $inventario->id_almacen }}
                    </div>
                    <div class="form-group mb-2 mb20">
                        <strong>Cantidad:</strong>
                        {{ $inventario->cantidad }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@stop

