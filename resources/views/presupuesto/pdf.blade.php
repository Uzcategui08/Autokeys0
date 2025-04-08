<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto - {{ $presupuesto->id_presupuesto }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            position: relative; 
            min-height: 100vh; 
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 45%;
            border: 1px solid #000;
            padding: 10px;
        }
        .info-box th, .info-box td {
            border: none; 
            padding: 8px;
            text-align: left;
        }
        .info-box th {
            background-color: #f2f2f2;
        }
        .date-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals th, .totals td {
            padding: 8px;
            text-align: right;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .footer table {
            width: 100%;
            border-collapse: collapse;
            height: 100px;
            padding: 20px;
        }
        .footer td {
            width: 50%;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 12px;
        }
        .footer p {
            margin-bottom: 15px;
        }
        hr {
            border: 1px solid #000;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="" alt="Logo de la empresa" style="float: right; max-height: 50px;">
        <h1 style="text-align: left;">Presupuesto Nº {{ $presupuesto->id_presupuesto }}</h1>
    </div>

    <div class="info-container">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Datos de la empresa</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong> Nombre de la empresa</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong> Dirección de la empresa</td>
                        </tr>
                        <tr>
                            <td><strong>CUIT-NIF:</strong> CUIT-NIF de la empresa</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong> Teléfono de la empresa</td>
                        </tr>
                        <tr>
                            <td><strong>E-mail:</strong> Email de la empresa</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Datos del cliente</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong> {{ $presupuesto->cliente->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong> {{ $presupuesto->cliente->direccion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>CUIT-NIF:</strong> {{ $presupuesto->cliente->cuit ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong> {{ $presupuesto->cliente->telefono ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>E-mail:</strong> {{ $presupuesto->cliente->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="date-line">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;"><strong>Fecha presupuesto:</strong> {{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('Y/m/d') }}</td>
                <td style="text-align: right;">
                    <strong>Validez:</strong> 
                    @php
                        $f_presupuesto = \Carbon\Carbon::parse($presupuesto->f_presupuesto);
                        $fechaValidez = \Carbon\Carbon::parse($presupuesto->validez);
                        $diferenciaDias = $f_presupuesto->diffInDays($fechaValidez);
                    @endphp
                    Hasta {{ $fechaValidez->format('Y/m/d') }} ({{ $diferenciaDias }} días)
                </td>
            </tr>
        </table>
    </div>

    @if (is_array($presupuesto->items))
        @foreach ($presupuesto->items as $itemGroup)
            <h3>Trabajo: {{ $itemGroup['trabajo'] ?? 'N/A' }}</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>DESCRIPCIÓN</th>
                        <th>UNIDADES</th>
                        <th>PRECIO</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($itemGroup['productos']) && is_array($itemGroup['productos']))
                        @foreach ($itemGroup['productos'] as $item)
                            <tr>
                                <td>{{ $item['nombre_producto'] ?? 'N/A' }}</td>
                                <td>{{ $item['cantidad'] ?? 'N/A' }}</td>
                                <td>${{ number_format($item['precio_producto'], 2) }}</td>
                                <td>${{ number_format($item['cantidad'] * $item['precio_producto'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        @endforeach
    @endif

    <div class="totals">
        @php
            $subtotal = 0;
            foreach ($presupuesto->items as $itemGroup) {
                if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                    foreach ($itemGroup['productos'] as $item) {
                        $subtotal += $item['cantidad'] * $item['precio_producto'];
                    }
                }
            }
            
            $descuentoAmount = $subtotal * ($presupuesto->descuento / 100);
            $subtotalConDescuento = $subtotal - $descuentoAmount;
            $ivaAmount = $subtotalConDescuento * ($presupuesto->iva / 100);
            $total = $subtotalConDescuento + $ivaAmount;
        @endphp
    
        <table>
            <tr>
                <th>SUBTOTAL</th>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <th>DESCUENTO ({{ $presupuesto->descuento }}%)</th>
                <td>${{ number_format($descuentoAmount, 2) }}</td>
            </tr>
            <tr>
                <th>IVA ({{ $presupuesto->iva }}%)</th>
                <td>${{ number_format($ivaAmount, 2) }}</td>
            </tr>
            <tr>
                <th><strong>TOTAL PRESUPUESTADO</strong></th>
                <td><strong>${{ number_format($total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Firma de la persona que confecciona el presupuesto</p>
                    <hr style="border: 1px solid #000; margin-top: 50px;">
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Firma de aceptación del cliente</p>
                    <hr style="border: 1px solid #000;margin-top: 50px;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
