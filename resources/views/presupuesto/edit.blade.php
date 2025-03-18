@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Editar</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Presupuesto') }}</span>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-secondary btn-sm" href="{{ route('presupuestos.index') }}"> {{ __('Volver') }}</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('presupuestos.update', $presupuesto->id_presupuesto) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('presupuesto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
