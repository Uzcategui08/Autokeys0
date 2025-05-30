<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo #{{ $registroV->id }}</title>
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
            <h2>RECIBO DE VENTA</h2>
            <p>N° {{ $registroV->id }}</p>
            <p>Fecha: {{ \Carbon\Carbon::parse($registroV->fecha_h)->format('d/m/Y') }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td class="info-label">Cliente:</td>
                            <td class="info-value">{{ $registroV->cliente }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Teléfono:</td>
                            <td class="info-value">{{ $registroV->telefono }}</td>
                        </tr>
                        @if($registroV->marca || $registroV->modelo || $registroV->año)
                        <tr>
                            <td class="info-label">Vehículo:</td>
                            <td class="info-value">{{ $registroV->marca }} {{ $registroV->modelo }}@if($registroV->año) ({{ $registroV->año }})@endif</td>
                        </tr>
                        @endif
                    </table>
                </td>

                <td>
                    <table width="100%">
                        <tr>
                            <td class="info-label">Técnico:</td>
                            <td class="info-value">{{ $registroV->empleado->nombre }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Estatus:</td>
                            <td class="info-value">
                                <span class="estatus 
                                    @if($registroV->estatus == 'pagado') pagado
                                    @elseif($registroV->estatus == 'parcialementep') parcial
                                    @elseif($registroV->estatus == 'pendiente') pendiente
                                    @else otro
                                    @endif">
                                    {{ ucfirst($registroV->estatus) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @foreach($items as $itemGroup)
            @if(isset($itemGroup['trabajo']) && isset($itemGroup['productos']))
                <div class="titulo-trabajo" style="margin-top: 15px; margin-bottom: 5px;">
                    <strong>{{ $itemGroup['trabajo'] }}</strong>
                </div>

                @if(isset($itemGroup['descripcion']) && $itemGroup['descripcion'])
                <div class="trabajo-descripcion">
                    <strong>Descripción:</strong> {{ $itemGroup['descripcion'] }}
                </div>
                @endif

                @if(isset($itemGroup['precio_trabajo']) && $itemGroup['precio_trabajo'])
                <div class="trabajo-precio">
                    <strong>Precio:</strong> ${{ number_format($itemGroup['precio_trabajo'], 2) }}
                </div>
                @endif

                <table class="productos-table">
                    <thead>
                        <tr>
                            <th width="15%">Cant.</th>
                            <th width="45%">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemGroup['productos'] as $producto)
                        <tr>
                            <td>{{ $producto['cantidad'] ?? 0 }}</td>
                            <td>{{ $producto['nombre_producto'] ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        <div class="totales-section">
            <div class="total-row total-grande">
                <span>TOTAL:</span>
                <span>${{ number_format($registroV->valor_v, 2) }}</span>
            </div>

        </div>

        <div class="section-title">HISTORIAL DE PAGOS</div>
        
        @if(count($registroV->pagos) > 0)
            <table class="pagos-table">
                <thead>
                    <tr>
                        <th width="25%">Fecha</th>
                        <th width="35%">Método</th>
                        <th width="40%" style="text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registroV->pagos as $pago)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $metodoPago = collect($tiposDePago)->firstWhere('id', $pago['metodo_pago'] ?? null);
                                @endphp
                                {{ $metodoPago->name ?? ($pago['metodo_pago'] ?? 'N/A') }}
                            </td>
                            <td style="text-align: right;">${{ number_format($pago['monto'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="resumen-pagos">
                <div class="resumen-row">
                    <span>Total Pagado:</span>
                    <span>${{ number_format($total_pagado, 2) }}</span>
                </div>
                <div class="resumen-row resumen-total">
                    <span>Saldo Pendiente:</span>
                    <span>${{ number_format($saldo_pendiente, 2) }}</span>
                </div>
            </div>
        @else
            <div class="no-pagos">No se han registrado pagos</div>
        @endif

        <div class="footer">
            <p>¡Gracias por su preferencia!</p>
            <div class="divider"></div>
            <p>Este documento es válido como comprobante de pago</p>
        </div>
    </div>
</body>
</html>