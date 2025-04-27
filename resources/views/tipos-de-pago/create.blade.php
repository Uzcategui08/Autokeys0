@extends('adminlte::page')

@section('title', 'Tipos de Pago')

@section('content_header')
<h1>Tipos de Pago</h1>
@stop

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Tipos De Pago</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('tipos-de-pagos.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('tipos-de-pago.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
