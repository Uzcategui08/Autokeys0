<!DOCTYPE html>
<html>
<head>
    <title>Reporte Fin de Semana - Van Grande/Pulga</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .positive { color: green; }
        .negative { color: red; }
        .header { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Fin de Semana</h2>
        <h3>Van Grande-Pulga y Van Pequeña-pulga</h3>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Fin de Semana</th>
                <th>Lugar</th>
                <th class="text-right">Ventas</th>
                <th class="text-right">% Comisión</th>
                <th class="text-right">Costos Items</th>
                <th class="text-right">Otros Costos</th>
                <th class="text-right">Gastos</th>
                <th class="text-right">Ganancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte as $item)
            <tr>
                <td>{{ Carbon\Carbon::parse($item['inicio'])->format('d/m') }} - {{ Carbon\Carbon::parse($item['fin'])->format('d/m/Y') }}</td>
                <td>{{ $item['lugarventa'] }}</td>
                <td class="text-right">${{ number_format($item['total_ventas'], 2) }}</td>
                <td class="text-right">${{ number_format($item['total_porcentaje'], 2) }}</td>
                <td class="text-right">${{ number_format($item['total_items'], 2) }}</td>
                <td class="text-right">${{ number_format($item['total_Costo'], 2) }}</td>
                <td class="text-right">${{ number_format($item['total_Gasto'], 2) }}</td>
                <td class="text-right {{ $item['ganancia'] >= 0 ? 'positive' : 'negative' }}">
                    ${{ number_format($item['ganancia'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>