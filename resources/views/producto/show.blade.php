@extends('adminlte::page')

@section('template_title')
    {{ $producto->name ?? __('Show') . " " . __('Producto') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Producto</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('productos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Producto:</strong>
                                    {{ $producto->id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Item:</strong>
                                    {{ $producto->item }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Marca:</strong>
                                    {{ $producto->marca }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>T Llave:</strong>
                                    {{ $producto->t_llave }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Sku:</strong>
                                    {{ $producto->sku }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Precio:</strong>
                                    {{ $producto->precio }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
