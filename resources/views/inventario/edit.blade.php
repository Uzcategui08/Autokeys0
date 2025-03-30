@extends('adminlte::page')

@section('title', 'Inventario')

@section('content_header')
<h1>Editar inventario</h1>
@stop

@section('content')
<section class="content container-fluid">
    <div class="">
        <div class="col-md-12">

            <div class="card card-default">
                <div class="card-header">
                    <span class="card-title">{{ __('Update') }} Inventario</span>
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

@section('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
@stop