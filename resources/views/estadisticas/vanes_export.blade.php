{{-- No extender adminlte para exportaciones PDF/Excel --}}
    <style>
        :root {
            --primary: #1363DF;
            --primary-dark: #0b4fb6;
            --muted: #6c757d;
            --bg: #f7f9fc;
            --table-header: #e9f1ff;
            --border: #dbe2ea;
            --success: #198754;
            --danger: #dc3545;
            --warning: #FFC107;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, Arial, 'Helvetica Neue', sans-serif;
            background: var(--bg);
            color: #1f2937;
            margin: 18px;
        }
        h2 {
            color: var(--primary);
            margin: 18px 0 8px;
            border-left: 4px solid var(--primary);
            padding-left: 8px;
        }
        h3 { color: #0b1220; margin: 14px 0 8px; }
        .resumen { margin-bottom: 14px; color: var(--muted); }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: #fff;
        }
        .badge-success { background: var(--success); }
        .badge-warning { background: var(--warning); color: #111; }
        .badge-danger { background: var(--danger); }
        .badge-secondary { background: #6c757d; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        thead th {
            background: var(--table-header);
            color: #111827;
            border: 1px solid var(--border);
            padding: 8px;
            font-weight: 600;
            text-align: center;
        }
        tbody td {
            border: 1px solid var(--border);
            padding: 8px;
            text-align: center;
        }
        tbody tr:nth-child(even) { background: #fbfdff; }
        .section { margin-top: 18px; }
        .small { font-size: 12px; color: var(--muted); }
        .highlight {
            background: #f0f7ff;
            border: 1px solid var(--border);
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 8px;
        }
    </style>
        .resumen {
            margin-bottom: 20px;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <h2>Estadísticas de Vans y Técnicos</h2>
    <div class="resumen">Desde: <b>{{ $startDate }}</b> — Hasta: <b>{{ $endDate }}</b></div>
    <div class="grid section">
        <div class="card">
            <div class="small">{{ $vanGrande }}</div>
            <div>Ventas: ${{ number_format($totales['ventasGrande'],2) }} | Costos: ${{ number_format($totales['costosGrande'],2) }} | Gastos: ${{ number_format($totales['gastosGrande'],2) }}</div>
            <div>Utilidad ajustada: <b>${{ number_format($totales['utilidadGrandeAjustada'] ?? $totales['utilidadGrande'], 2) }}</b></div>
        </div>
        <div class="card">
            <div class="small">{{ $vanPequena }}</div>
            <div>Ventas: ${{ number_format($totales['ventasPequena'],2) }} | Costos: ${{ number_format($totales['costosPequena'],2) }} | Gastos: ${{ number_format($totales['gastosPequena'],2) }}</div>
            <div>Utilidad ajustada: <b>${{ number_format($totales['utilidadPequenaAjustada'] ?? $totales['utilidadPequena'], 2) }}</b></div>
        </div>
    </div>

    <div class="section">
        <h2>Resumen de Adicionales por Van</h2>
        <table>
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
                    <td>${{ number_format($totales['gastosExtraGrande'] ?? 0, 2) }}</td>
                    <td>${{ number_format($totales['costosExtraGrande'] ?? 0, 2) }}</td>
                    <td>${{ number_format(($totales['gastosExtraGrande'] ?? 0) + ($totales['costosExtraGrande'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>{{ $vanPequena }}</td>
                    <td>${{ number_format($totales['gastosExtraPequena'] ?? 0, 2) }}</td>
                    <td>${{ number_format($totales['costosExtraPequena'] ?? 0, 2) }}</td>
                    <td>${{ number_format(($totales['gastosExtraPequena'] ?? 0) + ($totales['costosExtraPequena'] ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <table>
        <thead>
            <tr>
                <th colspan="2">Van</th>
                <th>Ventas</th>
                <th>Costos</th>
                <th>Gastos</th>

                <h2 class="section">Items Vendidos - {{ $vanGrande }}</h2>
                <table>
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
                            <td>${{ number_format($item['total_valor'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">Sin items</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <h2 class="section">Items Vendidos - {{ $vanPequena }}</h2>
                <table>
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
                            <td>${{ number_format($item['total_valor'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">Sin items</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <h2 class="section">Llaves Vendidas por Técnico</h2>
                <table>
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
                            <td>${{ number_format($tec['total_valor'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">Sin llaves registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <h2 class="section">Gastos por Van</h2>
                <h3>{{ $vanGrande }}</h3>
                <table>
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
                            <td>${{ number_format($g->valor, 2) }}</td>
                            <td>{{ ucfirst($g->estatus) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Sin gastos</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>{{ $vanPequena }}</h3>
                <table>
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
                            <td>${{ number_format($g->valor, 2) }}</td>
                            <td>{{ ucfirst($g->estatus) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Sin gastos</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>Gastos Adicionales</h3>
                <table>
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
                            <td>${{ number_format($g->valor, 2) }}</td>
                            <td>{{ $g->van ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Sin gastos adicionales</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h2 class="section">Costos por Van</h2>
                <h3>{{ $vanGrande }}</h3>
                <table>
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
                            <td>${{ number_format($c->valor, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3">Sin costos</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>{{ $vanPequena }}</h3>
                <table>
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
                            <td>${{ number_format($c->valor, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3">Sin costos</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>Costos Adicionales</h3>
                <table>
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
                            <td>${{ number_format($c->valor, 2) }}</td>
                            <td>{{ $c->van ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">Sin costos adicionales</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <th>Items</th>
                <th>Llaves</th>
                <th>Utilidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">{{ $vanGrande }}</td>
                <td>${{ number_format($totales['ventasGrande'], 2) }}</td>
                <td>${{ number_format($totales['costosGrande'], 2) }}</td>
                <td>${{ number_format($totales['gastosGrande'], 2) }}</td>
                <td>{{ $totales['itemsGrande'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>${{ number_format($totales['utilidadGrande'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ $vanPequena }}</td>
                <td>${{ number_format($totales['ventasPequena'], 2) }}</td>
                <td>${{ number_format($totales['costosPequena'], 2) }}</td>
                <td>${{ number_format($totales['gastosPequena'], 2) }}</td>
                <td>{{ $totales['itemsPequena'] }}</td>
                <td>{{ $totales['totalLlaves'] }}</td>
                <td>${{ number_format($totales['utilidadPequena'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
