
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 14px; margin-top: 0; }
        .section { margin-bottom: 15px; page-break-inside: avoid; }
        .section-title { background-color: #f5f5f5; padding: 5px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table th { background-color: #f2f2f2; text-align: left; padding: 5px; }
        table td { padding: 5px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { font-weight: bold; }
        .page-break { page-break-after: always; }
        .signature { margin-top: 50px; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Período: {{ $mes }}</p>
        <p>Generado el: {{ $date }}</p>
    </div>

    <!-- Resumen General -->
    <div class="section">
        <div class="section-title">RESUMEN GENERAL DEL MES</div>
        <table>
            <tr>
                <td>Facturación Total:</td>
                <td class="text-right">${{ number_format($stats['ventas']['facturacion'], 2) }}</td>
            </tr>
            <tr>
                <td>Cobrado Total:</td>
                <td class="text-right">${{ number_format($stats['ventas']['cobrado_mes'], 2) }}</td>
            </tr>
            <tr>
                <td>N° de Transacciones:</td>
                <td class="text-right">{{ $stats['ventas']['num_transacciones'] }}</td>
            </tr>
            <tr>
                <td>Ticket Promedio:</td>
                <td class="text-right">${{ number_format($stats['ventas']['ticket_promedio'], 2) }}</td>
            </tr>
            <tr>
                <td>N° de Trabajos Realizados:</td>
                <td class="text-right">{{ $totalTrabajos }}</td>
            </tr>
        </table>
    </div>

    <!-- Análisis de Utilidades -->
    <div class="section">
        <div class="section-title">ANÁLISIS DE UTILIDADES</div>
        <table>
            <tr>
                <td>Utilidad Bruta:</td>
                <td class="text-right">${{ number_format($stats['costos']['utilidad_bruta'], 2) }}</td>
                <td class="text-right">{{ number_format($stats['costos']['porcentaje_utilidad_bruta'], 2) }}%</td>
            </tr>
            <tr>
                <td>Total Gastos:</td>
                <td class="text-right">${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
                <td class="text-right">{{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%</td>
            </tr>
            <tr class="totals">
                <td>Utilidad Neta:</td>
                <td class="text-right">${{ number_format($stats['resultados']['utilidad_neta'], 2) }}</td>
                <td class="text-right">{{ number_format($stats['resultados']['porcentaje_utilidad_neta'], 2) }}%</td>
            </tr>
        </table>
    </div>

    <!-- Detalle de Gastos por Subcategoría -->
    <div class="section">
        <div class="section-title">DETALLE DE GASTOS POR SUBCATEGORÍA</div>
        <table>
            <thead>
                <tr>
                    <th>Subcategoría</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">% Facturación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['gastos']['por_subcategoria'] as $gasto)
                <tr>
                    <td>{{ $gasto['nombre'] }}</td>
                    <td class="text-right">${{ number_format($gasto['total'], 2) }}</td>
                    <td class="text-right">{{ number_format($gasto['porcentaje'], 2) }}%</td>
                </tr>
                @endforeach
                <tr class="totals">
                    <td>Total Gastos</td>
                    <td class="text-right">${{ number_format($stats['gastos']['total_gastos'], 2) }}</td>
                    <td class="text-right">{{ number_format($stats['gastos']['porcentaje_gastos'], 2) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
@php
use Carbon\Carbon;
@endphp
    <!-- Detalle de Transacciones -->
    <div class="section">
        <div class="section-title">DETALLE DE TRANSACCIONES ({{ $registros->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th class="text-right">Valor</th>
                    <th>Estatus</th>
                    <th>Trabajos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                <tr>
                    <td>{{ Carbon::parse($registro->fecha_h)->format('d/m/Y') }}</td>
                    <td>{{ $registro->cliente }}</td>
                    <td>{{ $registro->empleado->nombre ?? 'N/A' }}</td>
                    <td class="text-right">${{ number_format($registro->valor_v, 2) }}</td>
                    <td>{{ $registro->estatus }}</td>
                    <td>
                        @php
                            $items = json_decode($registro->items, true);
                            $trabajos = array_column($items, 'trabajo_nombre');
                        @endphp
                        {{ implode(', ', $trabajos) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Detalle de Gastos -->
    <div class="section">
        <div class="section-title">DETALLE DE GASTOS ({{ $gastos->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Subcategoría</th>
                    <th>Descripción</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gastos as $gasto)
                <tr>
                    <td>{{ Carbon::parse($gasto->f_gastos)->format('d/m/Y') }}</td>
                    <td>{{ $gasto->subcategoria }}</td>
                    <td>{{ $gasto->descripcion ?? 'N/A' }}</td>
                    <td class="text-right">${{ number_format($gasto->valor, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Firma -->
    <div class="signature">
        <p>Generado por: {{ auth()->user()->name }}</p>
        <div class="signature-line"></div>
        <p>Firma Autorizada</p>
    </div>
</body>
</html>