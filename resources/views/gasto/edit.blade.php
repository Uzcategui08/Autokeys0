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
                        <span class="card-title">{{ __('Update') }} Gasto</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('gastos.update', $gasto->id_gastos) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('gasto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
