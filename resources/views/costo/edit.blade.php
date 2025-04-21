@extends('adminlte::page')

@section('title', 'Descuento')

@section('content_header')
<h1>Editar</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Costo</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('costos.update', $costo->id_costos) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('costo.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
