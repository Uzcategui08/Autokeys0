@extends('adminlte::page')

@push('css')
<style>
    .toggle-category-btn {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        border: 1px solid transparent;
        background: transparent;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        padding: 0;
        transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }
    .toggle-category-btn:hover,
    .toggle-category-btn:focus {
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.35);
        outline: none;
        box-shadow: none;
    }
    .toggle-category-btn[aria-expanded="true"] {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .toggle-category-btn i {
        transition: transform 0.2s ease;
    }
    .toggle-category-btn[aria-expanded="true"] i {
        transform: rotate(90deg);
    }
    .category-detail-panel {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.45rem;
        padding: 0.5rem 0.75rem;
    }
    .category-detail-panel table td,
    .category-detail-panel table th {
        border: none;
        padding: 0.2rem 0.35rem;
        font-size: 0.82rem;
    }
</style>
@endpush

@section('title', 'Estadísticas Financieras')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
        <h1>Reporte Financiero - {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}</h1>
        
        <form method="GET" action="{{ route('estadisticas.ventas') }}" class="form-inline">
            <div class="form-group mr-2">
                <select name="month" class="form-control">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == $monthSelected ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mr-2">
                <select name="year" class="form-control">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            
            @if(request()->has('month') || request()->has('year'))
                <a href="{{ route('estadisticas.ventas') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
        </form>
    </div>
@stop

@section('content')
@if($noData)
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h3>No hay datos de ventas para {{ DateTime::createFromFormat('!m', $monthSelected)->format('F') }} de {{ $yearSelected }}</h3>
        <p class="mt-3">Por favor, seleccione otro período.</p>
    </div>
@else
<div class="container-fluid px-0">  <!-- px-0 para eliminar padding horizontal -->
    <div class="card shadow-sm rounded-0" style="border-left: 4px solid #0d6efd;">  <!-- rounded-0 para esquinas rectas -->
    <div class="card-body p-4">
<form action="{{ route('generatePdfTotal.pdf') }}" method="GET" target="_blank">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label small mb-1">Fecha Inicio</label>
            <input type="date" class="form-control form-control-sm" id="fecha_inicio" name="fecha_inicio" 
                   value="{{ old('fecha_inicio', $fecha_inicio ?? '') }}">
        </div>
        <div class="col-md-4">
            <label for="fecha_fin" class="form-label small mb-1">Fecha Fin</label>
            <input type="date" class="form-control form-control-sm" id="fecha_fin" name="fecha_fin" 
                   value="{{ old('fecha_fin', $fecha_fin ?? '') }}">
        </div>
        <input type="hidden" name="month" value="{{ $monthSelected }}">
        <input type="hidden" name="year" value="{{ $yearSelected }}">
        <div class="col-md-4">
            <button type="submit" class="btn btn-sm btn-primary mt-3">
                <i class="fas fa-file-pdf me-1"></i> Generar PDF
            </button>
        </div>
    </div>
</form>
    </div>
</div>
</div>
    <div class="card mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h3 class="card-title">Resumen Ejecutivo</h3>
            <div class="card-tools">
                <span class="badge bg-light text-dark">
                    {{ \Carbon\Carbon::create($yearSelected, $monthSelected, 1)->format('F Y') }}
                </span>
            </div>
<button onclick="window.print()" class="btn btn-secondary">
    <i class="fas fa-print"></i> Imprimir Página
</button>
        </div>
        
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 metrics-row">
                <div class="col">
                    <div class="card mini-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="metric-label text-primary"><i class="fas fa-chart-line mr-1"></i>Facturación</div>
                            <div class="metric-value">${{ number_format($stats['ventas']['facturacion'], 2) }}</div>
                            <div class="metric-meta">
                                Cobrado: <strong>${{ number_format($stats['ventas']['cobrado'], 2) }}</strong>
                                <span class="chip chip-light ml-2">Contado ${{ number_format($stats['ventas']['ingresos_contado'], 2) }}</span>
                                <span class="chip chip-light">Recibidos ${{ number_format($stats['ventas']['ingresos_recibidos'], 2) }}</span>
                                <div class="mt-2 small text-muted">
                                    {{ $stats['ventas']['num_transacciones'] }} transacciones · Ticket ${{ number_format($stats['ventas']['ticket_promedio'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mini-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="metric-label text-success"><i class="fas fa-donate mr-1"></i>Utilidad Bruta</div>
                            <div class="metric-value">${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</div>
                            <div class="metric-meta">
                                {{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}% de facturación
                                <div class="mt-2 small text-muted">
                                    Costo venta: ${{ number_format($stats['costos']['total_costo_venta'], 2) }}
                                    <span class="chip chip-outline">{{ number_format($stats['costos']['porcentaje_costo_venta'], 2) }}% fact.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mini-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="metric-label text-warning"><i class="fas fa-briefcase mr-1"></i>Utilidad Operativa</div>
                            <div class="metric-value">${{ number_format($stats['resultados']['utilidad_operativa'], 2) }}</div>
                            <div class="metric-meta">
                                {{ number_format($stats['resultados']['porcentaje_utilidad_operativa'], 2) }}% de facturación
                                <div class="mt-2 small text-muted">
                                    Gastos (sin retiros): ${{ number_format($stats['gastos']['total_gastos'] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mini-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="metric-label text-info"><i class="fas fa-wallet mr-1"></i>Utilidad Neta</div>
                            <div class="metric-value">${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</div>
                            <div class="metric-meta">
                                {{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}% de facturación
                                <div class="mt-2 small text-muted">
                                    Retiros del dueño: ${{ number_format($stats['gastos']['retiros_dueno'] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>Detalle de Ventas</th>
                            <th>Valor</th>
                            <th>% Facturación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Facturación Total</td>
                            <td>${{ number_format($stats['ventas']['facturacion'], 2) }}</td>
                            <td>100.00%</td>
                        </tr>
                        <tr>
                            <td>Cobrado </td>
                            <td>${{ number_format($stats['ventas']['cobrado'], 2) }}</td>
                            <td>{{ number_format(($stats['ventas']['cobrado'] / max($stats['ventas']['facturacion'], 1)) * 100, 2) }}%</td>
                        </tr>
                        <tr>
                            <td>Evolución Facturación (vs mes anterior)</td>
                            <td colspan="2">{{ number_format($stats['ventas']['evolucion_facturacion'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalle de Costos y Gastos -->
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th>Detalle de Costos y Gastos</th>
                        <th>Valor</th>
                        <th>% Facturación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-section-title">
                        <td colspan="3">
                            <span class="section-pill"><i class="fas fa-wallet mr-2"></i>Costos</span>
                        </td>
                    </tr>   
                        <tr>
                            <td><strong>Costo de Venta</strong></td>
                            <td>${{ number_format($stats['costos']['total_costos_mes'], 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-between align-items-center" style="gap:0.5rem;">
                                    <span>{{ number_format($stats['costos']['porcentaje_total_costos'], 2) }}%</span>
                                    <button class="btn btn-link p-0 toggle-detail" type="button" data-toggle="collapse" data-target="#detalleCostos" aria-expanded="false" aria-controls="detalleCostos">
                                        <i class="fas fa-chevron-down mr-1"></i> Ver detalle ({{ count($stats['costos']['detalle'] ?? []) }})
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @if(!empty($stats['costos']['categorias']))
                    <tr class="table-section-title">
                        <td colspan="3">
                            <span class="section-pill"><i class="fas fa-layer-group mr-2"></i>Costos por categoría</span>
                        </td>
                    </tr>
                        @foreach($stats['costos']['categorias'] as $concepto)
                        <tr>
                            <td class="align-middle">
                                &nbsp;&nbsp;{{ $concepto['nombre'] }}
                                @if(!empty($concepto['subcategorias']))
                                    <button class="btn toggle-category-btn ml-2" type="button" data-toggle="collapse" data-target="#detalleCostCategoria{{ $loop->index }}" aria-expanded="false" aria-controls="detalleCostCategoria{{ $loop->index }}" aria-label="Ver conceptos de {{ $concepto['nombre'] }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif
                            </td>
                            <td>${{ number_format($concepto['total'], 2) }}</td>
                            <td>
                                <div>
                                    <span class="font-weight-bold">{{ number_format($concepto['porcentaje'], 2) }}%</span>
                                    <small class="text-muted text-uppercase" style="letter-spacing: .05em;">sobre facturación</small>
                                </div>
                            </td>
                        </tr>
                        @if(!empty($concepto['subcategorias']))
                        <tr class="collapse" id="detalleCostCategoria{{ $loop->index }}">
                            <td colspan="3" class="border-0 pt-0">
                                <div class="pl-4 pb-3">
                                    <div class="category-detail-panel">
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                @foreach($concepto['subcategorias'] as $sub)
                                                <tr>
                                                    <td class="text-muted">{{ $sub['nombre'] }}</td>
                                                    <td class="text-right font-weight-bold">${{ number_format($sub['total'], 2) }}</td>
                                                    <td class="text-right text-muted">{{ number_format($sub['porcentaje'] ?? 0, 2) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    @endif
                                  <tr>
                        <td><strong>Utilidad Bruta</strong></td>
                        <td>${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</td>
                        <td>{{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}%</td>
                    </tr>
                    
                    <tr>
                        <td colspan="3" class="p-0 border-0">
                            <div class="collapse" id="detalleCostos">
                                <div class="p-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-detail align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Categoría</th>
                                                    <th>Valor</th>
                                                    <th>% Facturación</th>
                                                    <th>Origen</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($stats['costos']['detalle'] ?? [] as $detalle)
                                                    <tr>
                                                        <td>{{ $detalle['fecha'] ? \Carbon\Carbon::parse($detalle['fecha'])->format('d/m/Y') : 'N/D' }}</td>
                                                        <td>{{ $detalle['descripcion'] }}</td>
                                                        <td>{{ $detalle['categoria_padre'] ?? $detalle['subcategoria'] }}</td>
                                                        <td>${{ number_format($detalle['valor'], 2) }}</td>
                                                        <td>{{ number_format($detalle['porcentaje_facturacion'] ?? 0, 2) }}%</td>
                                                        <td>
                                                            @php
                                                                $fuente = $detalle['fuente'] ?? 'directo';
                                                                $badgeClass = 'primary';
                                                                $fuenteTexto = 'Costo directo';
                                                                if ($fuente === 'nomina') {
                                                                    $badgeClass = 'info';
                                                                    $fuenteTexto = 'Nómina';
                                                                } elseif ($fuente === 'llaves') {
                                                                    $badgeClass = 'warning';
                                                                    $fuenteTexto = 'Llaves';
                                                                }
                                                            @endphp
                                                            <span class="badge badge-{{ $badgeClass }}">{{ $fuenteTexto }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Sin registros de costos para este mes.</td>
                                                    </tr>
                                                @endforelse
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @if(!empty($stats['gastos']['categorias']))
                    <tr class="table-section-title">
                        <td colspan="3">
                            <span class="section-pill"><i class="fas fa-wallet mr-2"></i>Gastos por categoría</span>
                        </td>
                    </tr>             
                        @foreach($stats['gastos']['categorias'] as $item)
                        <tr>
                            <td class="align-middle">
                                &nbsp;&nbsp;{{ $item['nombre'] }}
                                @if(!empty($item['subcategorias']))
                                    <button class="btn toggle-category-btn ml-2" type="button" data-toggle="collapse" data-target="#detalleGastoCategoria{{ $loop->index }}" aria-expanded="false" aria-controls="detalleGastoCategoria{{ $loop->index }}" aria-label="Ver conceptos de {{ $item['nombre'] }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif
                            </td>
                            <td>${{ number_format($item['total'], 2) }}</td>
                            <td>
                                <div>
                                    <span class="font-weight-bold">{{ number_format($item['porcentaje'], 2) }}%</span>
                                    <small class="text-muted text-uppercase" style="letter-spacing: .05em;">sobre facturación</small>
                                </div>
                            </td>
                        </tr>
                        @if(!empty($item['subcategorias']))
                        <tr class="collapse" id="detalleGastoCategoria{{ $loop->index }}">
                            <td colspan="3" class="border-0 pt-0">
                                <div class="pl-4 pb-3">
                                    <div class="category-detail-panel">
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                @foreach($item['subcategorias'] as $sub)
                                                <tr>
                                                    <td class="text-muted">{{ $sub['nombre'] }}</td>
                                                    <td class="text-right font-weight-bold">${{ number_format($sub['total'], 2) }}</td>
                                                    <td class="text-right text-muted">{{ number_format($sub['porcentaje'] ?? 0, 2) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    @endif
                        <tr>
                            <td><strong>Total Gastos</strong></td>
                            <td>${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-between align-items-center" style="gap:0.5rem;">
                                    <span>{{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%</span>
                                    <button class="btn btn-link p-0 toggle-detail" type="button" data-toggle="collapse" data-target="#detalleGastos" aria-expanded="false" aria-controls="detalleGastos">
                                        <i class="fas fa-chevron-down mr-1"></i> Ver detalle ({{ count($stats['gastos']['detalle'] ?? []) }})
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <td colspan="3" class="p-0 border-0">
                            <div class="collapse" id="detalleGastos">
                                <div class="p-3">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-hover table-detail align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Descripción</th>
                                                <th>Categoría</th>
                                                <th>Valor</th>
                                                <th>% Facturación</th>
                                                <th>Origen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @forelse($stats['gastos']['detalle'] ?? [] as $detalle)
                                                <tr>
                                                    <td>{{ $detalle['fecha'] ? \Carbon\Carbon::parse($detalle['fecha'])->format('d/m/Y') : 'N/D' }}</td>
                                                    <td>{{ $detalle['descripcion'] }}</td>
                                                    <td>{{ $detalle['categoria_padre'] ?? $detalle['subcategoria'] }}</td>
                                                    <td>${{ number_format($detalle['valor'], 2) }}</td>
                                                    <td>{{ number_format($detalle['porcentaje_facturacion'] ?? 0, 2) }}%</td>
                                                    <td>
                                                        @php
                                                            $fuente = $detalle['fuente'] ?? 'directo';
                                                            $badgeClass = 'secondary';
                                                            $fuenteTexto = 'Gasto directo';
                                                            if ($fuente === 'nomina') {
                                                                $badgeClass = 'info';
                                                                $fuenteTexto = 'Nómina';
                                                            } elseif ($fuente === 'retiro') {
                                                                $badgeClass = 'dark';
                                                                $fuenteTexto = 'Retiro dueño';
                                                            }
                                                        @endphp
                                                        <span class="badge badge-{{ $badgeClass }}">{{ $fuenteTexto }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Sin registros de gastos para este mes.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                        <tr class="table-section-title">
                        <td colspan="3">
                            <span class="section-pill"><i class="fas fa-wallet mr-2"></i>Resultados</span>
                        </td>
                    </tr>   
      
                    <tr>
                        <td><strong>Utilidad Operativa</strong></td>
                        <td>${{ number_format($stats['resultados']['utilidad_operativa'], 2) }}</td>
                        <td>{{ number_format($stats['resultados']['porcentaje_utilidad_operativa'], 2) }}%</td>
                    </tr>
                    @if(($stats['gastos']['retiros_dueno'] ?? 0) > 0)
                    <tr>
                        <td>Retiro del Dueño</td>
                        <td>${{ number_format($stats['gastos']['retiros_dueno'], 2) }}</td>
                        <td>{{ number_format($stats['gastos']['porcentaje_retiros_dueno'], 2) }}%</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Utilidad Neta</strong></td>
                        <td>${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</td>
                        <td>{{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}%</td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

@endif
@stop

@section('css')
<style>
    :root {
        --ak-blue: #0d6efd;
        --ak-gray: #6c757d;
        --ak-soft-bg: #f8f9fb;
    }

    .card-header {
        font-weight: bold;
    }
    .metrics-row .mini-card {
        border-radius: 0.75rem;
        background: linear-gradient(180deg, #fff, #f7f9ff);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .metrics-row .mini-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.65rem 1.25rem rgba(13, 110, 253, 0.12);
    }
    .metric-label {
        font-size: .85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .metric-value {
        font-size: 1.65rem;
        font-weight: 700;
        margin: .35rem 0 .45rem;
        color: #111;
    }
    .metric-meta {
        font-size: .85rem;
        color: var(--ak-gray);
        line-height: 1.4;
    }
    .chip {
        display: inline-flex;
        align-items: center;
        padding: 0.1rem 0.55rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        border: 1px solid transparent;
    }
    .chip-light {
        background: #eef4ff;
        color: #1b4fbc;
    }
    .chip-outline {
        border-color: #cfd5e4;
        color: #5c6c89;
        background: transparent;
    }
    .table th {
        background-color: #343a40;
        color: white;
    }
    .table-modern {
        background: #fff;
        border-radius: .75rem;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
    }
    .table-modern thead th {
        background: #11192f;
        border: none;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .08em;
    }
    .table-modern tbody tr:nth-child(odd) {
        background: #fafbfd;
    }
    .table-modern tbody tr:hover {
        background-color: rgba(13, 110, 253, .07);
    }
    .table-detail thead {
        background: var(--ak-soft-bg);
    }
    .table-detail tbody tr:hover {
        background: rgba(0,0,0,.03);
    }
    .table-section-title td {
        background: #eef4ff;
        border-top: 2px solid var(--ak-blue);
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .78rem;
        color: #0d2b66;
    }
    .section-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 700;
    }
    .toggle-detail {
        font-weight: 600;
        color: var(--ak-blue);
        text-decoration: none;
    }
    .toggle-detail:hover {
        text-decoration: underline;
    }
    .toggle-detail i {
        transition: transform .2s ease;
    }
    .toggle-detail[aria-expanded="true"] i {
        transform: rotate(180deg);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .container-fluid, .container-fluid * {
            visibility: visible;
        }
        .container-fluid {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header, .table th {
            background-color: #343a40 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    }
</style>
@stop