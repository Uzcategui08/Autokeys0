<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Nómina - {{ $empleado->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 15mm;
            background-color: #f9f9f9;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        .recibo-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a5276;
            position: relative;
        }
        
        .logo-placeholder {
            width: 80px;
            height: 80px;
            background-color: #f1f1f1;
            border: 1px dashed #ccc;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1a5276;
        }
        
        .recibo-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #333;
        }
        
        .periodo {
            font-size: 14px;
            color: #555;
            background-color: #f1f1f1;
            padding: 3px 10px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 5px;
        }
        
        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
            align-items: stretch;
        }
        
        .info-box {
            border: 1px solid #e0e0e0;
            padding: 12px;
            width: 48%;
            border-radius: 5px;
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .info-title {
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
            color: #1a5276;
            font-size: 13px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background-color: #1a5276;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            font-size: 12px;
        }
        
        .table td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            vertical-align: top;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .totals {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 15px;
        }
        
        .total-box {
            width: 48%;
            border: 1px solid #e0e0e0;
            padding: 12px;
            border-radius: 5px;
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            height: 1px;
            border-top: 1px solid #333;
            margin: 20px auto 5px;
            width: 80%;
        }
        
        .signature-label {
            font-size: 11px;
            color: #555;
            margin-top: 5px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        
        .negative {
            color: #e74c3c;
        }
        
        .positive {
            color: #27ae60;
        }
        
        .neto-box {
            width: 100%; 
            max-width: 500px; 
            margin: 15px auto; 
            background-color: #eaf2f8;
            border: 1px solid #aed6f1;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
            box-sizing: border-box;
        }
        
        .neto-title {
            font-weight: bold;
            color: #1a5276;
            font-size: 14px;
        }
        
        .neto-amount {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
            color: #1a5276;
        }
        
        .section-title {
            background-color: #f1f1f1;
            font-weight: bold;
            padding: 5px 8px;
            margin: 10px 0 5px;
            border-left: 3px solid #1a5276;
        }
        .employee-info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px; 
            margin-bottom: 20px;
        }

        .employee-info-table td {
            vertical-align: top;
        }

        .info-box {
            width: 50%; 
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .totals-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
            margin: 20px 0;
        }

        .totals-table td {
            vertical-align: top;
        }

        .total-box {
            width: 50%;
            border: 1px solid #e0e0e0;
            padding: 12px;
            border-radius: 5px;
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #1a5276;
            font-size: 13px;
        }

        .positive {
            color: #27ae60;
        }

        .negative {
            color: #e74c3c;
        }

        .text-bold {
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 20px; 
            margin-top: 40px;
        }

        .signature-table td {
            vertical-align: top;
            width: 50%;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            width: 80%;
            height: 1px;
            border-top: 1px solid #333;
            margin: 0 auto 5px;
        }

        .signature-label {
            font-size: 11px;
            color: #555;
        }

    </style>
</head>
<body>
    <div class="recibo-container">

        <div class="header">
            <div class="logo-placeholder">LOGO AUTOKEYS</div>
            <div class="company-name">AUTOKEYS</div>
            <div class="recibo-title">RECIBO DE PAGO DE NÓMINA</div>
            <div class="periodo">Período: {{ date('d/m/Y', strtotime($periodo->inicio)) }} al {{ date('d/m/Y', strtotime($periodo->fin)) }}</div>
        </div>
        
        <table class="employee-info-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="info-box">
                    <div class="info-title">INFORMACIÓN DEL EMPLEADO</div>
                    <div><strong>Nombre:</strong> {{ $empleado->nombre }}</div>
                    <div><strong>ID Empleado:</strong> {{ $empleado->id_empleado }}</div>
                    <div><strong>Tipo de Nómina:</strong> {{ $empleado->tipoNomina->nombre ?? 'N/A' }}</div>
                </td>
                <td class="info-box">
                    <div class="info-title">DETALLES DEL PAGO</div>
                    <div><strong>Fecha de Pago:</strong> {{ now()->format('d/m/Y') }}</div>
                    <div><strong>Frecuencia:</strong> {{ $periodo->tipo->frecuencia_nombre }}</div>
                    <div><strong>Método de Pago:</strong> Transferencia Bancaria</div>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th width="25%">CONCEPTO</th>
                    <th width="55%">DESCRIPCIÓN</th>
                    <th width="20%" class="text-right">VALOR</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Sueldo Base</td>
                    <td>Salario base según contrato</td>
                    <td class="text-right">{{ number_format($empleado->salario_base, 2) }}</td>
                </tr>

                <tr>
                    <td>Devengado</td>
                    <td>Total devengado (Sueldo Base * Factor)</td>
                    <td class="text-right">{{ number_format($totalPagado, 2) }}</td>
                </tr>

                @if($totalDescuentos > 0 && $descuentos instanceof \Illuminate\Support\Collection && $descuentos->isNotEmpty())
                <tr>
                    <td colspan="3" class="section-title">DESCUENTOS</td>
                </tr>
                @foreach($descuentos as $descuento)
                <tr>
                    <td>Descuento</td>
                    <td>{{ $descuento->concepto }} ({{ $descuento->d_fecha }})</td>
                    <td class="text-right negative">-{{ number_format($descuento->valor, 2) }}</td>
                </tr>
                @endforeach
                @endif

                @if($totalCostos > 0 && $costos instanceof \Illuminate\Support\Collection && $costos->isNotEmpty())
                <tr>
                    <td colspan="3" class="section-title">COSTOS</td>
                </tr>
                @foreach($costos as $costo)
                <tr>
                    <td>Costo</td>
                    <td>{{ $costo->descripcion }} ({{ $costo->f_costos }})</td>
                    <td class="text-right negative">-{{ number_format($costo->valor, 2) }}</td>
                </tr>
                @endforeach
                @endif

                @if($totalAbonos > 0 && $abonos instanceof \Illuminate\Support\Collection && $abonos->isNotEmpty())
                <tr>
                    <td colspan="3" class="section-title">ABONOS</td>
                </tr>
                @foreach($abonos as $abono)
                <tr>
                    <td>Abono</td>
                    <td>{{ $abono->concepto }} ({{ $abono->a_fecha}})</td>
                    <td class="text-right positive">+{{ number_format($abono->valor, 2) }}</td>
                </tr>
                @endforeach
                @endif

                @if(count($prestamos) > 0)
                <div style="margin: 12px 0; border-left: 3px solid #1a5276; padding-left: 12px;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px;">
                        <thead>
                            <tr>
                                <th style="background-color: #f8f9fa; padding: 6px 8px; text-align: left; border: 1px solid #e0e0e0; font-weight: bold; color: #1a5276;">PRÉSTAMO</th>
                                <th style="background-color: #f8f9fa; padding: 6px 8px; text-align: center; border: 1px solid #e0e0e0; font-weight: bold; color: #1a5276;">CUOTA</th>
                                <th style="background-color: #f8f9fa; padding: 6px 8px; text-align: right; border: 1px solid #e0e0e0; font-weight: bold; color: #1a5276;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prestamos as $p)
                            <tr>
                                <td style="padding: 6px 8px; border: 1px solid #e0e0e0;">Préstamo #{{ $p['numero_prestamo'] }}</td>
                                <td style="padding: 6px 8px; border: 1px solid #e0e0e0; text-align: center;">Cuota #{{ $p['cuota_actual'] }}</td>
                                <td style="padding: 6px 8px; border: 1px solid #e0e0e0; text-align: right; color: #e74c3c;">-{{ number_format($p['monto_prestamo'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </tbody>
        </table>
        
        <table class="totals-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="total-box">
                    <div class="info-title">RESUMEN DE DEVENGADOS</div>
                    <div>Total Devengado: {{ number_format($totalPagado, 2) }}</div>
                    @if($totalAbonos > 0)
                    <div>Total Abonos: <span class="positive">+{{ number_format($totalAbonos, 2) }}</span></div>
                    @endif
                    <div class="text-bold" style="margin-top: 8px; border-top: 1px solid #e0e0e0; padding-top: 5px;">
                        Total a Pagar: {{ number_format($totalPagado + $totalAbonos, 2) }}
                    </div>
                </td>
                <td class="total-box">
                    <div class="info-title">RESUMEN DE DEDUCCIONES</div>
                    @if($totalDescuentos > 0)
                    <div>Total Descuentos: <span class="negative">-{{ number_format($totalDescuentos, 2) }}</span></div>
                    @endif
                    @if($totalPrestamos > 0)
                    <div>Total Préstamos: <span class="negative">-{{ number_format($totalPrestamos, 2) }}</span></div>
                    @endif
                    @if($totalCostos > 0)
                    <div>Total Costos: <span class="negative">-{{ number_format($totalCostos, 2) }}</span></div>
                    @endif
                    <div class="text-bold" style="margin-top: 8px; border-top: 1px solid #e0e0e0; padding-top: 5px;">
                        Total Deducciones: <span class="negative">-{{ number_format($totalDescuentos + $totalPrestamos + $totalCostos, 2) }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="neto-box">
            <div class="neto-title">NETO A PAGAR</div>
            <div class="neto-amount">${{ number_format($netoPagado, 2) }}</div>
        </div>

        <table class="signature-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">FIRMA EMPLEADO</div>
                </td>
                <td class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">FIRMA RESPONSABLE</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Documento generado electrónicamente por AutoKeys el {{ now()->format('d/m/Y H:i:s') }}<br>
        </div>
    </div>
</body>
</html>