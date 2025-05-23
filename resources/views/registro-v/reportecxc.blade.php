<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cuenta por Cobrar</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .reporte-container {
            width: 95%;
            max-width: 210mm;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 1px solid #bbb;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 13px;
            margin: 0 0 6px 0;
            font-weight: normal;
        }
        .header p {
            margin: 0;
            font-size: 11px;
        }
        .info-reporte {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .info-reporte td {
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #bbb;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 10px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
        }
        th {
            font-size: 10px;
            font-weight: bold;
            background: #f7f7f7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            font-weight: bold;
            background: #f7f7f7;
        }
        .cliente-header {
            font-weight: bold;
            font-size: 12px;
            margin: 18px 0 6px 0;
            padding: 6px 0 2px 0;
            border-bottom: 1px solid #bbb;
        }
        .estado-pago {
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border: 1px solid #bbb;
            border-radius: 2px;
            background: #fff;
            color: #222;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }
        .resumen-section {
            margin-top: 18px;
            padding: 10px 0 0 0;
            border-top: 1px solid #bbb;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }
        .resumen-total {
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
        }
        .pagos-detalle {
            padding: 6px 0 0 0;
            font-size: 10px;
        }
        .pago-item {
            display: flex;
            justify-content: flex-start;
            gap: 18px;
            margin-bottom: 2px;
        }
        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #bbb;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="reporte-container">
        <div class="header">
            <h1>Recibo General</h1>
            <h2>Resumen de movimientos</h2>
            <p>Generado el: {{ date('d/m/Y') }}</p>
        </div>

        <table class="info-reporte">
            <tr>
                <td>
                    <strong>PERÍODO:</strong>
                    {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                </td>
                <td>
                    <strong>SALDO PENDIENTE:</strong>
                    ${{ number_format($totalSaldo, 2) }}
                </td>
            </tr>
        </table>

        @foreach($data as $item)
        <div class="cliente-header">
            {{ $item->cliente }}
        </div>
        <table>
            <colgroup>
                <col style="width: 12%;">
                <col style="width: 10%;">
                <col style="width: 18%;">
                <col style="width: 13%;">
                <col style="width: 13%;">
                <col style="width: 13%;">
                <col style="width: 11%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-center">Factura</th>
                    <th class="text-center">Trabajos</th>
                    <th class="text-right">Monto</th>
                    <th class="text-right">Pagado</th>
                    <th class="text-right">Saldo</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->ventas as $venta)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha_h)->format('d/m/Y') }}</td>
                    <td class="text-center">#{{ $venta->id }}</td>
                    <td>
                        @foreach($venta->trabajos as $trabajo)
                            <div>
                                <strong>{{ $trabajo['nombre'] }}</strong>
                                <br>
                                <small>{{ $trabajo['descripcion'] }}</small>
                            </div>
                        @endforeach
                    </td>
                    <td class="text-right">${{ number_format($venta->valor_v, 2) }}</td>
                    <td class="text-right">${{ number_format($venta->total_pagado, 2) }}</td>
                    <td class="text-right">${{ number_format($venta->valor_v - $venta->total_pagado, 2) }}</td>
                    <td class="text-center">
                        @if($venta->valor_v == $venta->total_pagado)
                            <span class="estado-pago">Pagado</span>
                        @elseif($venta->total_pagado > 0)
                            <span class="estado-pago">Parcial</span>
                        @else
                            <span class="estado-pago">Pendiente</span>
                        @endif
                    </td>
                </tr>
@if($venta->pagos && count($venta->pagos) > 0)
<tr>
    <td colspan="7">
        <div class="pagos-detalle">
            <strong>Detalle de pagos:</strong>
            <ul style="list-style: none; padding-left: 0; margin: 8px 0 0 0;">
                @foreach($venta->pagos as $pago)
                <li style="margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px solid #eee;">
                    <span><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</span><br>
                    <span><strong>Monto:</strong> ${{ number_format($pago['monto'], 2) }}</span><br>
                    <span><strong>Método:</strong>
                        @php
                            $metodoPago = collect($tiposDePago)->firstWhere('id', $pago['metodo_pago'] ?? null);
                        @endphp
                        {{ $metodoPago->name ?? ($pago['metodo_pago'] ?? 'N/A') }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
    </td>
</tr>
@endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3">TOTAL CLIENTE</td>
                    <td class="text-right">${{ number_format($item->total_ventas_monto, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_pagado, 2) }}</td>
                    <td class="text-right">${{ number_format($item->saldo_pendiente, 2) }}</td>
                    <td class="text-center">
                        @if($item->total_ventas_monto > 0)
                            {{ number_format(($item->total_pagado / $item->total_ventas_monto) * 100, 2) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        @endforeach

        <div class="resumen-section">
            <div class="resumen-row">
                <span>Total Ventas:</span>
                <span>${{ number_format($data->sum('total_ventas_monto'), 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Total Pagado:</span>
                <span>${{ number_format($data->sum('total_pagado'), 2) }}</span>
            </div>
            <div class="resumen-row resumen-total">
                <span>Saldo Pendiente:</span>
                <span>${{ number_format($totalSaldo, 2) }}</span>
            </div>
            <div class="resumen-row">
                <span>Porcentaje Pagado:</span>
                <span>
                    @if($data->sum('total_ventas_monto') > 0)
                        {{ number_format(($data->sum('total_pagado') / $data->sum('total_ventas_monto')) * 100, 2) }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
        </div>

        <div class="footer">
            <p>Este documento es un reporte generado para fines informativos del cliente.</p>
        </div>
    </div>
</body>
</html>