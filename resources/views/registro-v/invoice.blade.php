<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $registroV->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #333;
        }
        .recibo-container {
            width: 140mm;
            max-width: 140mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
            padding-bottom: 3mm;
            border-bottom: 1px dashed #ccc;
        }
        .header h1 {
            font-size: 14px;
            margin: 2mm 0;
            color: #000;
        }
        .header p {
            margin: 1mm 0;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        .info-table td {
            padding: 2mm 1mm;
            vertical-align: top;
            border: none;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            width: 70%;
            word-break: break-word;
        }
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
            font-size: 10px;
        }
        .productos-table th {
            text-align: left;
            padding: 2mm 1mm;
            border-bottom: 1px solid #000;
        }
        .productos-table td {
            padding: 1mm;
            border-bottom: 1px dashed #eee;
        }
        .text-right {
            text-align: right;
        }
        .totales-section {
            margin-top: 5mm;
            border-top: 1px dashed #ccc;
            padding-top: 3mm;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .total-grande {
            font-weight: bold;
            font-size: 14px;
            margin-top: 2mm;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #ccc;
            padding-top: 3mm;
        }
        .pagos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5mm;
            font-size: 10px;
        }
        .pagos-table th {
            text-align: left;
            padding: 2mm 1mm;
            border-bottom: 1px solid #000;
        }
        .pagos-table td {
            padding: 2mm 1mm;
            border-bottom: 1px dashed #eee;
        }
        .resumen-pagos {
            background-color: #f5f5f5;
            padding: 2mm;
            margin-top: 3mm;
            border-radius: 2mm;
            font-size: 10px;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .resumen-total {
            font-weight: bold;
            color: #2c3e50;
        }
        .divider {
            border-top: 1px dashed #ccc;
            margin: 3mm 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2mm;
            color: #2c3e50;
        }
        .no-pagos {
            text-align: center;
            font-style: italic;
            padding: 3mm 0;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="recibo-container">
        <div class="header">
            <div class="divider"></div>
            <h2>SALES RECEIPT</h2>
            <p>No. {{ $registroV->id }}</p>
            <p>Date: {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('m/d/Y') }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td class="info-label">Customer:</td>
                            <td class="info-value">{{ $registroV->cliente }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone:</td>
                            <td class="info-value">{{ $registroV->telefono }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Vehicle:</td>
                            <td class="info-value">{{ $registroV->marca }} {{ $registroV->modelo }} ({{ $registroV->año }})</td>
                        </tr>
                    </table>
                </td>

                <td>
                    <table width="100%">
                        <tr>
                            <td class="info-label">Technician:</td>
                            <td class="info-value">{{ $registroV->tecnico }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Service:</td>
                            <td class="info-value">{{ $registroV->trabajo }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Status:</td>
                            <td class="info-value">
                                <span class="status 
                                    @if($registroV->estatus == 'pagado') paid
                                    @elseif($registroV->estatus == 'parcialementep') partial
                                    @elseif($registroV->estatus == 'pendiente') pending
                                    @else other
                                    @endif">
                                    @if($registroV->estatus == 'pagado') Paid
                                    @elseif($registroV->estatus == 'parcialementep') Partial
                                    @elseif($registroV->estatus == 'pendiente') Pending
                                    @else Other
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="productos-table">
            <thead>
                <tr>
                    <th width="15%">Qty.</th>
                    <th width="45%">Description</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($registroV->items as $itemGroup)
                    @foreach($itemGroup['productos'] as $producto)
                        @php
                            $productoSubtotal = $producto['cantidad'] * $producto['precio_producto'];
                            $subtotal += $productoSubtotal;
                        @endphp
                        <tr>
                            <td>{{ $producto['cantidad'] }}</td>
                            <td>{{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="totales-section">
            <div class="total-row total-grande">
                <span>TOTAL:</span>
                <span>${{ number_format($registroV->valor_v, 2) }}</span>
            </div>
        </div>

        <div class="section-title">PAYMENT HISTORY</div>
        
        @if(count($registroV->pagos) > 0)
            <table class="pagos-table">
                <thead>
                    <tr>
                        <th width="25%">Date</th>
                        <th width="35%">Method</th>
                        <th width="40%" style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registroV->pagos as $pago)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pago['fecha'])->format('m/d/Y') }}</td>
                            <td>{{ ucfirst($pago['metodo_pago']) }}</td>
                            <td style="text-align: right;">${{ number_format($pago['monto'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="resumen-pagos">
                <div class="resumen-row">
                    <span>Total Paid:</span>
                    <span>${{ number_format($registroV->total_pagado, 2) }}</span>
                </div>
                <div class="resumen-row resumen-total">
                    <span>Balance Due:</span>
                    <span>${{ number_format($registroV->saldo_pendiente, 2) }}</span>
                </div>
            </div>
        @else
            <div class="no-pagos">No payments recorded</div>
        @endif

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <div class="divider"></div>
            <p>This document serves as payment receipt</p>
        </div>
    </div>
</body>
</html>