@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1>Editar</h1>
@stop

@section('content')
<section class="content container-fluid">
    <div class="">
        <div class="col-md-12">

            <div class="card card-default">
                <div class="card-header">
                    <span class="card-title">Inventario</span>
                </div>
                <div class="card-body bg-white">
                    <form method="POST" action="{{ route('inventarios.update', $inventario->id_inventario) }}" role="form" enctype="multipart/form-data">
                        {{ method_field('PATCH') }}
                        @csrf

                        @include('inventario.form')

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop