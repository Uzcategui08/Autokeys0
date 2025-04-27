<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget - {{ $presupuesto->id_presupuesto }}</title>
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
        <img src="" alt="Company logo" style="float: right; max-height: 50px;">
        <h1 style="text-align: left;">Budget No. {{ $presupuesto->id_presupuesto }}</h1>
    </div>

    <div class="info-container">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Company Information</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong> Company Name</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong> Company Address</td>
                        </tr>
                        <tr>
                            <td><strong>Tax ID:</strong> Company Tax ID</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong> Company Phone</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong> Company Email</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <th style="width: 100%; font-size: 12px; text-align: left;">Customer Information</th>
                        </tr>
                        <tr>
                            <td><hr style="border: 1px solid #000;"></td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong> {{ $presupuesto->cliente->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong> {{ $presupuesto->cliente->direccion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tax ID:</strong> {{ $presupuesto->cliente->cuit ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong> {{ $presupuesto->cliente->telefono ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong> {{ $presupuesto->cliente->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="date-line">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;"><strong>Budget Date:</strong> {{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('Y/m/d') }}</td>
                <td style="text-align: right;">
                    <strong>Valid Until:</strong> 
                    @php
                        $f_presupuesto = \Carbon\Carbon::parse($presupuesto->f_presupuesto);
                        $fechaValidez = \Carbon\Carbon::parse($presupuesto->validez);
                        $diferenciaDias = $f_presupuesto->diffInDays($fechaValidez);
                    @endphp
                    Until {{ $fechaValidez->format('Y/m/d') }} ({{ $diferenciaDias }} days)
                </td>
            </tr>
        </table>
    </div>

    @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DESCRIPTION</th>
                    <th>UNIT PRICE</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                @endphp
                @foreach ($presupuesto->items as $index => $item)
                    @php
                        $subtotal += $item['precio'] ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['descripcion'] ?? 'No description available' }}</td>
                        <td>${{ number_format($item['precio'] ?? 0, 2) }}</td>
                        <td>${{ number_format($item['precio'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No items in this budget</p>
    @endif

    <div class="totals">
        @php
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
            @if($presupuesto->descuento > 0)
            <tr>
                <th>DISCOUNT ({{ $presupuesto->descuento }}%)</th>
                <td>-${{ number_format($descuentoAmount, 2) }}</td>
            </tr>
            <tr>
                <th>SUBTOTAL WITH DISCOUNT</th>
                <td>${{ number_format($subtotalConDescuento, 2) }}</td>
            </tr>
            @endif
            @if($presupuesto->iva > 0)
            <tr>
                <th>VAT ({{ $presupuesto->iva }}%)</th>
                <td>${{ number_format($ivaAmount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <th><strong>TOTAL AMOUNT</strong></th>
                <td><strong>${{ number_format($total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Budget prepared by</p>
                    <hr style="border: 1px solid #000; margin-top: 50px;">
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top; font-size: 12px;">
                    <p style="margin-bottom: 15px;">Customer acceptance</p>
                    <hr style="border: 1px solid #000;margin-top: 50px;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>