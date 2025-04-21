@extends('adminlte::page')

@section('title', 'Prestamos')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Nempleados') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('nempleados.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Id Nempleado</th>
									<th >Id Pnomina</th>
									<th >Id Empleado</th>
									<th >Total Descuentos</th>
									<th >Total Abonos</th>
									<th >Total Prestamos</th>
									<th >Total Pagado</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nempleados as $nempleado)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $nempleado->id_nempleado }}</td>
										<td >{{ $nempleado->id_pnomina }}</td>
										<td >{{ $nempleado->id_empleado }}</td>
										<td >{{ $nempleado->total_descuentos }}</td>
										<td >{{ $nempleado->total_abonos }}</td>
										<td >{{ $nempleado->total_prestamos }}</td>
										<td >{{ $nempleado->total_pagado }}</td>

                                            <td>
                                                <form action="{{ route('nempleados.destroy', $nempleado->id_empleado) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('nempleados.show', $nempleado->id_empleado) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('nempleados.edit', $nempleado->id_empleado) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $nempleados->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
