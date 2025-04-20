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
                                {{ __('Cuotas') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('cuotas.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Id Cuotas</th>
									<th >Id Prestamos</th>
									<th >Valor</th>
									<th >Fecha Vencimiento</th>
									<th >Pagada</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cuotas as $cuota)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $cuota->id_cuotas }}</td>
										<td >{{ $cuota->id_prestamos }}</td>
										<td >{{ $cuota->valor }}</td>
										<td >{{ $cuota->fecha_vencimiento }}</td>
										<td >{{ $cuota->pagada }}</td>

                                            <td>
                                                <form action="{{ route('cuotas.destroy', $cuota->id_cuotas) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('cuotas.show', $cuota->id_cuotas) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('cuotas.edit', $cuota->id_cuotas) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $cuotas->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
