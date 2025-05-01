@extends('adminlte::page')

@section('title', 'Tipos de Pago')

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
                                {{ __('Tipos De Pagos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('tipos-de-pagos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
                                  {{ __('Crear Nuevo') }}
                                </a>
                              </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTable">
                                <thead class="thead">
                                    <tr>
                                    <th>ID</th>
									<th >Nombre</th>
                                    <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiposDePagos as $tiposDePago)
                                        <tr>
                                        <td>{{ $tiposDePago->id }}</td>    
										<td >{{ $tiposDePago->name }}</td>

                                            <td>
                                                <form onsubmit="return confirmDelete(this)"  action="{{ route('tipos-de-pagos.destroy', $tiposDePago->id) }}" method="POST" class="delete-form" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('tipos-de-pagos.show', $tiposDePago->id) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('tipos-de-pagos.edit', $tiposDePago->id) }}"><i class="fa fa-fw fa-edit"></i></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"> 
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
