<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Vans (Excel)</title>
</head>
<body>
    <h3>Estadísticas de Vans y Técnicos</h3>
    <p>Desde: <b>{{ $startDate }}</b> - Hasta: <b>{{ $endDate }}</b></p>

    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Van</th>
                <th>Ventas</th>
                <th>Costos</th>
                <th>Gastos</th>
                <th>Items</th>
                <th>Llaves</th>
                <th>Utilidad (ajustada)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $vanGrande }}</td>
                <td>{{ number_format($totales['ventasGrande'], 2) }}</td>
                <td>{{ number_format($totales['costosGrande'], 2) }}</td>
                <td>{{ number_format($totales['gastosGrande'], 2) }}</td>
                <td>{{ $totales['itemsGrande'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>{{ number_format($totales['utilidadGrandeAjustada'] ?? $totales['utilidadGrande'], 2) }}</td>
            </tr>
            <tr>
                <td>{{ $vanPequena }}</td>
                <td>{{ number_format($totales['ventasPequena'], 2) }}</td>
                <td>{{ number_format($totales['costosPequena'], 2) }}</td>
                <td>{{ number_format($totales['gastosPequena'], 2) }}</td>
                <td>{{ $totales['itemsPequena'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>{{ number_format($totales['utilidadPequenaAjustada'] ?? $totales['utilidadPequena'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Resumen de Adicionales por Van</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Van</th>
                <th>Gastos Adicionales</th>
                <th>Costos Adicionales</th>
                <th>Total Adicionales</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $vanGrande }}</td>
                <td>{{ number_format($totales['gastosExtraGrande'] ?? 0, 2) }}</td>
                <td>{{ number_format($totales['costosExtraGrande'] ?? 0, 2) }}</td>
                <td>{{ number_format(($totales['gastosExtraGrande'] ?? 0) + ($totales['costosExtraGrande'] ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>{{ $vanPequena }}</td>
                <td>{{ number_format($totales['gastosExtraPequena'] ?? 0, 2) }}</td>
                <td>{{ number_format($totales['costosExtraPequena'] ?? 0, 2) }}</td>
                <td>{{ number_format(($totales['gastosExtraPequena'] ?? 0) + ($totales['costosExtraPequena'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Items Vendidos - {{ $vanGrande }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasVanGrande['items'] as $item)
            <tr>
                <td>{{ $item['nombre'] }}</td>
                <td>{{ $item['total_cantidad'] }}</td>
                <td>{{ number_format($item['total_valor'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin items</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Items Vendidos - {{ $vanPequena }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasVanPequena['items'] as $item)
            <tr>
                <td>{{ $item['nombre'] }}</td>
                <td>{{ $item['total_cantidad'] }}</td>
                <td>{{ number_format($item['total_valor'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin items</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Llaves Vendidas por Técnico</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Total Llaves</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($llavesPorTecnico as $tec)
            <tr>
                <td>{{ $tec['tecnico'] }}</td>
                <td>{{ $tec['total_llaves'] }}</td>
                <td>{{ number_format($tec['total_valor'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin llaves registradas</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Gastos por Van — {{ $vanGrande }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastosVanGrande as $g)
            <tr>
                <td>{{ \Carbon\Carbon::parse($g->f_gastos)->format('m/d/Y') }}</td>
                <td>{{ $g->descripcion }}</td>
                <td>{{ number_format($g->valor, 2) }}</td>
                <td>{{ ucfirst($g->estatus) }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Sin gastos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Gastos por Van — {{ $vanPequena }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gastosVanPequena as $g)
            <tr>
                <td>{{ \Carbon\Carbon::parse($g->f_gastos)->format('m/d/Y') }}</td>
                <td>{{ $g->descripcion }}</td>
                <td>{{ number_format($g->valor, 2) }}</td>
                <td>{{ ucfirst($g->estatus) }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Sin gastos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Gastos Adicionales</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
                <th>Van</th>
            </tr>
        </thead>
        <tbody>
            @php $gastosExtra = $gastosExtraVanes ?? collect(); @endphp
            @forelse($gastosExtra as $g)
            <tr>
                <td>{{ \Carbon\Carbon::parse($g->f_gastos)->format('m/d/Y') }}</td>
                <td>{{ $g->descripcion }}</td>
                <td>{{ number_format($g->valor, 2) }}</td>
                <td>{{ $g->van ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Sin gastos adicionales</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Costos por Van — {{ $vanGrande }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costosVanGrande as $c)
            <tr>
                <td>{{ \Carbon\Carbon::parse($c->f_costos)->format('m/d/Y') }}</td>
                <td>{{ $c->descripcion }}</td>
                <td>{{ number_format($c->valor, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin costos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Costos por Van — {{ $vanPequena }}</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costosVanPequena as $c)
            <tr>
                <td>{{ \Carbon\Carbon::parse($c->f_costos)->format('m/d/Y') }}</td>
                <td>{{ $c->descripcion }}</td>
                <td>{{ number_format($c->valor, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin costos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Costos Adicionales</h4>
    <table border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Valor</th>
                <th>Van</th>
            </tr>
        </thead>
        <tbody>
            @php $costosExtra = $costosExtraVanes ?? collect(); @endphp
            @forelse($costosExtra as $c)
            <tr>
                <td>{{ \Carbon\Carbon::parse($c->f_costos)->format('m/d/Y') }}</td>
                <td>{{ $c->descripcion }}</td>
                <td>{{ number_format($c->valor, 2) }}</td>
                <td>{{ $c->van ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Sin costos adicionales</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
