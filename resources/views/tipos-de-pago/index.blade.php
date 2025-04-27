@extends('adminlte::page')

@section('title', 'Tipos de Pago')

@section('content_header')
<h1>Tipos de Pago</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Tipos De Pagos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('tipos-de-pagos.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
                                </a>
                              </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Name</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiposDePagos as $tiposDePago)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $tiposDePago->name }}</td>

                                            <td>
                                                <form action="{{ route('tipos-de-pagos.destroy', $tiposDePago->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('tipos-de-pagos.show', $tiposDePago->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('tipos-de-pagos.edit', $tiposDePago->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
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
                {!! $tiposDePagos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
