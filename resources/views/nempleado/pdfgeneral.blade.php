<!DOCTYPE html>
<html>
<head>
    <style>
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
        
        .reporte-container {
            width: 100%;
            max-width: 100%;
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
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1a5276;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            color: #555;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #1a5276;
            color: white;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        
        td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .empleado-row {
            background-color: #f5f5f5;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #eaf2f8;
            border-top: 2px solid #1a5276;
            border-bottom: 2px solid #1a5276;
        }
        
        .negative {
            color: #e74c3c;
        }
        
        .positive {
            color: #27ae60;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-bold {
            font-weight: bold;
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
        
        .prestamo-details {
            margin-top: 5px;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="reporte-container">
        <div class="header">
            <div class="logo-placeholder">LOGO AUTOKEYS</div>
            <h1>REPORTE GENERAL DE NÓMINA</h1>
            <p>Período: {{ date('m/d/Y', strtotime($periodo->inicio)) }} al {{ date('m/d/Y', strtotime($periodo->fin)) }}</p>
            <p>Documento generado electrónicamente por AutoKeys el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="25%">Nombre</th>
                    <th width="20%">Cargo</th>
                    <th width="12%" class="text-right">Salario Base</th>
                    <th width="12%" class="text-right">Total Pagado</th>
                    <th width="12%" class="text-right">Descuentos</th>
                    <th width="12%" class="text-right">Costos</th>
                    <th width="12%" class="text-right">Abonos</th>
                    <th width="12%" class="text-right">Préstamos</th>
                    <th width="12%" class="text-right">Neto Pagado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr class="empleado-row">
                    <td>{{ $empleado['nombre'] }}</td>
                    <td>{{ $empleado['cargo'] }}</td>
                    <td class="text-right">${{ number_format($empleado['salario_base'], 2) }}</td>
                    <td class="text-right">${{ number_format($empleado['totalPagado'], 2) }}</td>
                    <td class="text-right negative">${{ number_format($empleado['totalDescuentos'], 2) }}</td>
                    <td class="text-right negative">${{ number_format($empleado['totalCostos'], 2) }}</td>
                    <td class="text-right positive">${{ number_format($empleado['totalAbonos'], 2) }}</td>
                    <td class="text-right negative">
                        ${{ number_format($empleado['totalPrestamos'], 2) }}
                        @if(count($empleado['prestamos']) > 0)
                            <div class="prestamo-details">
                                @foreach($empleado['prestamos'] as $prestamo)
                                Préstamo #{{ $prestamo['numero'] }}, Cuota {{ $prestamo['cuota'] }}<br>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-right text-bold">${{ number_format($empleado['netoPagado'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="text-bold">TOTALES GENERALES</td>
                    <td class="text-right text-bold">${{ number_format(array_sum(array_column($empleados, 'salario_base')), 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($totales['totalPagado'], 2) }}</td>
                    <td class="text-right text-bold negative">${{ number_format($totales['totalDescuentos'], 2) }}</td>
                    <td class="text-right text-bold negative">${{ number_format($totales['totalCostos'], 2) }}</td>
                    <td class="text-right text-bold positive">${{ number_format($totales['totalAbonos'], 2) }}</td>
                    <td class="text-right text-bold negative">${{ number_format($totales['totalPrestamos'], 2) }}</td>
                    <td class="text-right text-bold">${{ number_format($totales['netoPagado'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>