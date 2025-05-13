<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de CxC Resumen</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .reporte-container {
            width: 90%;
            max-width: 210mm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 8mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid #2c3e50;
        }
        .header h1 {
            font-size: 18px;
            margin: 2mm 0;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 14px;
            margin: 1mm 0 3mm 0;
            color: #7f8c8d;
            font-weight: normal;
        }
        .header p {
            margin: 1mm 0;
            font-size: 11px;
            color: #666;
        }
        .info-reporte {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .info-reporte td {
            padding: 14px 12px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #e0e0e0;
        }

        .info-reporte td:last-child {
            border-right: none;
        }

        .info-label {
            font-weight: 600;
            color: #5a6a7a;
            margin-bottom: 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        .info-value {
            font-size: 16px;
            color: #2d3748;
            font-family: 'Roboto Mono', 'Courier New', monospace;
            font-weight: 500;
        }

        /* Estilos específicos para tipos de valores */
        .date-value {
            font-family: 'Segoe UI', Roboto, sans-serif;
            color: #4a5568;
        }

        .date-separator {
            color: #a0aec0;
            margin: 0 2px;
        }

        .number-value {
            font-weight: 600;
            color: #2b6cb0;
        }

        .money-value {
            font-weight: 600;
            color: #2f855a;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6mm 0;
            font-size: 10px;
            table-layout: fixed;
        }
        th {
            background-color: #2c3e50;
            color: white;
            text-align: left;
            padding: 3mm 2mm;
            font-weight: normal;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 2.5mm 2mm;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            word-wrap: break-word;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
            padding-right: 3mm;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f7fa !important;
        }
        .total-row td {
            border-top: 1px solid #ddd;
            border-bottom: none;
            padding: 3mm 2mm;
        }
        .footer {
            margin-top: 10mm;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 4mm;
            line-height: 1.6;
        }
        .estado-pago {
            display: inline-block;
            padding: 2mm 3mm;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            min-width: 60px;
            text-align: center;
        }
        .pagado {
            background-color: #27ae60;
            color: white;
        }
        .parcial {
            background-color: #f39c12;
            color: white;
        }
        .pendiente {
            background-color: #e74c3c;
            color: white;
        }
        .progress-bar {
            height: 6px;
            background-color: #ecf0f1;
            border-radius: 3px;
            margin: 3mm 0 1mm 0;
            overflow: hidden;
        }
        .progress {
            height: 100%;
            background-color: #27ae60;
        }
        .cliente-header {
            background-color: #f8f9fa;
            padding: 3mm;
            margin: 5mm 0 2mm 0;
            border-left: 4px solid #2c3e50;
            font-weight: bold;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 3px;
        }
        .metodo-pago {
            font-size: 9px;
            color: #7f8c8d;
            margin-left: 2mm;
        }
        .resumen-section {
            margin-top: 10mm;
            padding: 6mm;
            background-color: #f5f7fa;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3mm;
            align-items: center;
        }
        .resumen-total {
            font-weight: bold;
            font-size: 13px;
            margin-top: 4mm;
            padding-top: 3mm;
            border-top: 1px solid #ddd;
        }
        .pagos-detalle {
            padding: 3mm;
            background-color: #f9f9f9;
            border-radius: 3px;
            margin-top: 1mm;
            font-size: 9px;
        }
        .pago-item {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5mm;
            padding-bottom: 1.5mm;
            border-bottom: 1px dashed #e0e0e0;
        }
        .pago-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .col-fecha {
            width: 20%;
        }
        .col-factura {
            width: 10%;
        }
        .col-monto {
            width: 18%;
        }
        .col-pagado {
            width: 18%;
        }
        .col-saldo {
            width: 18%;
        }
        .col-estado {
            width: 16%;
        }
    </style>
</head>
<body>
    <div class="reporte-container">
        <div class="header">
            <h1>Reporte de Cuentas por Cobrar</h1>
            <h2>Resumen por Cliente</h2>
            <p>Generado el: {{ date('d/m/Y') }}</p>
        </div>

        <table class="info-reporte">
            <tr>
                <td class="info-box">
                    <div class="info-label">PERÍODO</div>
                    <div class="info-value date-value">
                        {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}<span class="date-separator"> - </span>{{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-label">TOTAL CLIENTES</div>
                    <div class="info-value number-value">{{ $data->count() }}</div>
                </td>
                <td class="info-box">
                    <div class="info-label">SALDO PENDIENTE</div>
                    <div class="info-value money-value">${{ number_format($totalSaldo, 2) }}</div>
                </td>
            </tr>
        </table>
        

        @foreach($data as $item)
        <div class="cliente-header">
            <span>{{ $item->cliente }}</span>
        </div>
        
        <table>
            <colgroup>
                <col class="col-fecha">
                <col class="col-factura">
                <col class="col-monto">
                <col class="col-pagado">
                <col class="col-saldo">
                <col class="col-estado">
            </colgroup>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-center">Factura</th>
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
                    <td class="text-center">
                        <span style="
                            display: inline-block;
                            padding: 3px 8px;
                            border-radius: 4px;
                            font-weight: bold;
                            background-color: #3498db;
                            color: white;
                            font-size: 10px;
                            min-width: 40px;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                        ">#{{ $venta->id }}</span>
                    </td>
                    <td class="text-right">${{ number_format($venta->valor_v, 2) }}</td>
                    <td class="text-right">${{ number_format($venta->total_pagado, 2) }}</td>
                    <td class="text-right">${{ number_format($venta->valor_v - $venta->total_pagado, 2) }}</td>
                    <td class="text-center">
                        @if($venta->valor_v == $venta->total_pagado)
                            <span class="estado-pago pagado">Pagado</span>
                        @elseif($venta->total_pagado > 0)
                            <span class="estado-pago parcial">Parcial</span>
                        @else
                            <span class="estado-pago pendiente">Pendiente</span>
                        @endif
                    </td>
                </tr>
                
                @if($venta->pagos && count($venta->pagos) > 0)
                <tr>
                    <td colspan="6" style="padding: 2mm 0 0 0;">
                        <div class="pagos-detalle">
                            <strong>Detalle de pagos:</strong>
                            @foreach($venta->pagos as $pago)
                                <div class="pago-item">
                                    <span>{{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</span>
                                    <span>${{ number_format($pago['monto'], 2) }}</span>
                                    <span class="metodo-pago">
                                        @php
                                            $metodoPago = collect($tiposDePago)->firstWhere('id', $pago['metodo_pago'] ?? null);
                                        @endphp
                                        {{ $metodoPago->name ?? ($pago['metodo_pago'] ?? 'N/A') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2">TOTAL CLIENTE</td>
                    <td class="text-right">${{ number_format($item->total_ventas_monto, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total_pagado, 2) }}</td>
                    <td class="text-right">${{ number_format($item->saldo_pendiente, 2) }}</td>
                    <td class="text-center">
                        <div style="margin-bottom: 2mm;">
                            @if($item->total_ventas_monto > 0)
                                {{ number_format(($item->total_pagado / $item->total_ventas_monto) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="width: {{ $item->total_ventas_monto > 0 ? ($item->total_pagado / $item->total_ventas_monto) * 100 : 0 }}%"></div>
                        </div>
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
            <p>Este documento es un reporte interno generado automáticamente</p>
        </div>
    </div>
</body>
</html>