<?php

namespace App\Http\Controllers;

use App\Models\Nempleado;
use App\Models\RegistroV;
use App\Models\Gasto;
use App\Models\Costo;
use App\Models\Categoria;
use App\Models\Empleado;
use App\Models\AjusteInventario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class EstadisticasVentasController extends Controller
{
    protected const GASTOS_CATEGORIAS_FIJAS = [
        'gastos-de-personal' => 'Gastos de Personal',
        'gastos-operativos' => 'Gastos operativos',
        'otros-gastos' => 'Otros Gastos',
        'financieros-e-impuestos' => 'Financieros e Impuestos'
    ];

    protected $month;
    protected $year;
    protected $availableYears;
    protected $nominaCostos = null;
    protected $nominaGastos = null;
    protected $detalleNominaCostos = null;
    protected $detalleNominaGastos = null;
    protected $categoriaCache = [];
    protected $retiroDuenoTotal = null;
    protected $detalleRetirosDueno = null;
    protected $costosLlavesTotal = null;
    protected $detalleCostosLlaves = null;

    public function __construct()
    {
        // Obtener años disponibles para el filtro
        $this->availableYears = $this->getAvailableYears();
    }

    public function index(Request $request)
    {
        $this->month = $request->input('month', date('m'));
        $this->year = $request->input('year', date('Y'));

        // Verificar si hay datos de ventas para el mes seleccionado
        $hasData = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->exists();

        $stats = $hasData ? $this->getAllStats() : null;

        return view('estadisticas.ventas', [
            'stats' => $stats,
            'monthSelected' => $this->month,
            'yearSelected' => $this->year,
            'availableYears' => $this->availableYears,
            'noData' => !$hasData // Pasamos explícitamente si no hay datos
        ]);
    }

    protected function getAvailableYears()
    {
        // Obtener años únicos de todas las tablas relevantes
        $yearsRegistroV = RegistroV::selectRaw('EXTRACT(YEAR FROM fecha_h) as year')
            ->distinct()
            ->pluck('year');

        $yearsGastos = Gasto::selectRaw('EXTRACT(YEAR FROM f_gastos) as year')
            ->distinct()
            ->pluck('year');

        $yearsCostos = Costo::selectRaw('EXTRACT(YEAR FROM f_costos) as year')
            ->distinct()
            ->pluck('year');

        // Combinar y obtener años únicos
        $allYears = $yearsRegistroV->merge($yearsGastos)->merge($yearsCostos)->unique()->sortDesc();

        return $allYears->values()->all();
    }

    // Métodos para estadísticas de ventas

    protected function cobradoDelMes()
    {
        $total = 0;

        // Traemos todos los registros con pagos (sin limitar por fecha de venta) para poder capturar ingresos de meses posteriores
        $registros = RegistroV::whereNotNull('pagos')->get();

        foreach ($registros as $registro) {
            $pagos = $registro->pagos;

            // Normalizar a array (puede venir como JSON)
            if (!is_array($pagos)) {
                $pagos = json_decode($pagos ?? '[]', true) ?? [];
            }

            foreach ($pagos as $pago) {
                // Extraer campos soportando array u objeto
                $fecha = is_array($pago)
                    ? ($pago['fecha'] ?? null)
                    : ($pago->fecha ?? null);

                $monto = is_array($pago)
                    ? ($pago['monto'] ?? 0)
                    : ($pago->monto ?? 0);

                // Si el pago tiene fecha propia, usamos esa
                if ($fecha) {
                    try {
                        $fechaPago = Carbon::parse($fecha);
                    } catch (\Exception $e) {
                        continue;
                    }

                    if (
                        (int) $fechaPago->year === (int) $this->year &&
                        (int) $fechaPago->month === (int) $this->month
                    ) {
                        $total += (float) $monto;
                    }

                    continue;
                }

                // Sin fecha en el pago: lo consideramos cobrado en el mes de la venta original
                $fechaVenta = Carbon::parse($registro->fecha_h);
                if (
                    (int) $fechaVenta->year === (int) $this->year &&
                    (int) $fechaVenta->month === (int) $this->month
                ) {
                    $total += (float) $monto;
                }
            }
        }

        return $total;
    }

    protected function ingresosRecibidosDelMes()
    {
        // Lógica equivalente a getIngresosRecibidos del cierre semanal: pagos cuya fecha cae en el mes
        // y corresponden a ventas de meses anteriores (créditos históricos).
        [$inicio, $fin] = $this->obtenerRangoMes();

        $empleados = \App\Models\Empleado::with(['ventas' => function ($query) {
            $query->whereNotNull('pagos')
                ->whereRaw("json_array_length(pagos) > 0");
        }])
            ->whereHas('ventas', function ($query) {
                $query->whereNotNull('pagos')
                    ->whereRaw("json_array_length(pagos) > 0");
            })
            ->get();

        $total = 0;

        foreach ($empleados as $empleado) {
            foreach ($empleado->ventas as $venta) {
                $pagos = $this->parsePagosVenta($venta->pagos ?? '[]');
                $fechaVenta = $venta->fecha_h ? \Carbon\Carbon::parse($venta->fecha_h) : null;

                foreach ($pagos as $pago) {
                    if (!isset($pago['fecha'], $pago['monto'])) {
                        continue;
                    }
                    try {
                        $fechaPago = \Carbon\Carbon::parse($pago['fecha']);
                    } catch (\Exception $e) {
                        continue;
                    }
                    if (!$fechaPago->between($inicio, $fin)) {
                        continue;
                    }
                    // Excluir pagos de ventas del mismo mes (solo ingresos de créditos históricos)
                    if ($fechaVenta && $fechaVenta->format('Ym') >= $fechaPago->format('Ym')) {
                        continue;
                    }
                    $total += (float) $pago['monto'];
                }
            }
        }
        return $total;
    }

    protected function parsePagosVenta($pagosData)
    {
        if (is_string($pagosData)) {
            $decoded = json_decode($pagosData, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($pagosData) ? $pagosData : [];
    }

    protected function ingresosAlContadoDelMes()
    {
        // Ventas al contado facturadas en el mes
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->where('tipo_venta', 'contado')
            ->sum('valor_v');
    }

    protected function facturacionDelMes()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->sum('valor_v');
    }

    protected function evolucionFacturacion()
    {
        // D5 = facturación del mes actual
        $facturacionActual = $this->facturacionDelMes();

        // H3 = facturación del mes anterior (referencia)
        $lastMonth = Carbon::create($this->year, $this->month, 1)->subMonth();
        $facturacionAnterior = RegistroV::whereYear('fecha_h', $lastMonth->year)
            ->whereMonth('fecha_h', $lastMonth->month)
            ->sum('valor_v');

        if ($facturacionActual <= 0 || $facturacionAnterior <= 0) {
            return 0;
        }

        // Fórmula Excel: =D5/H3
        return round($facturacionActual / $facturacionAnterior, 2);
    }

    protected function numeroTransacciones()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->count();
    }

    protected function ticketPromedio()
    {
        // Solo considerar ventas con items válidos
        $ventas = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get(['items', 'valor_v']);
        $totalTrabajos = 0;
        $totalFacturacion = 0;
        foreach ($ventas as $venta) {
            $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);
            $numItems = is_array($items) ? count($items) : 0;
            if ($numItems > 0) {
                $totalTrabajos += $numItems;
                $totalFacturacion += $venta->valor_v;
            }
        }
        return $totalTrabajos == 0 ? 0 : $totalFacturacion / $totalTrabajos;
    }

    // Métodos para costos y gastos
    protected function totalCostosDelMes()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor')
            + $this->obtenerNominaCostos()
            + $this->obtenerCostosLlavesTotal();
    }
    protected function totalCostoVenta()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor')
            + $this->obtenerNominaCostos()
            + $this->obtenerCostosLlavesTotal();
    }

    protected function totalGastoPersonal()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'personal')
            ->sum('valor');
    }

    protected function totalGastosOperativos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'operativos')
            ->sum('valor');
    }

    protected function totalOtrosGastos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'otros')
            ->sum('valor');
    }

    protected function totalFinancierosImpuestos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'financieros_impuestos')
            ->sum('valor');
    }

    protected function totalGastos()
    {
        // Total de gastos SIN incluir retiros del dueño
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->sum('valor') + $this->obtenerNominaGastos();
    }

    protected function obtenerNominaCostos()
    {
        if ($this->nominaCostos !== null) {
            return $this->nominaCostos;
        }

        $this->nominaCostos = Nempleado::whereYear('fecha_pago', $this->year)
            ->whereMonth('fecha_pago', $this->month)
            ->whereHas('empleado', function ($query) {
                $query->where('tipo', 1)
                    ->where('cargo', '!=', 5)
                    ->where('tipo_pago', '!=', 'retiro');
            })
            ->sum('total_pagado');

        return $this->nominaCostos;
    }

    protected function agruparDetallePorCategoria(array $detalle, float $totalBase): array
    {
        if (empty($detalle)) {
            return [];
        }

        $agrupado = [];

        foreach ($detalle as $item) {
            $valor = (float) ($item['valor'] ?? 0);
            if ($valor == 0) {
                continue;
            }

            $categoria = $item['categoria_padre'] ?? $item['subcategoria'] ?? 'Sin categoría';
            $subcategoria = $item['subcategoria'] ?? $categoria;

            if (! isset($agrupado[$categoria])) {
                $agrupado[$categoria] = [
                    'nombre' => $categoria,
                    'total' => 0,
                    'subcategorias' => []
                ];
            }

            $agrupado[$categoria]['total'] += $valor;
            $agrupado[$categoria]['subcategorias'][$subcategoria] = ($agrupado[$categoria]['subcategorias'][$subcategoria] ?? 0) + $valor;
        }

        return collect($agrupado)
            ->map(function ($categoria) use ($totalBase) {
                $categoria['porcentaje'] = $this->calcularPorcentaje($categoria['total'], $totalBase);
                $categoria['subcategorias'] = collect($categoria['subcategorias'])
                    ->map(function ($valor, $nombre) use ($totalBase) {
                        return [
                            'nombre' => $nombre,
                            'total' => $valor,
                            'porcentaje' => $this->calcularPorcentaje($valor, $totalBase)
                        ];
                    })
                    ->sortByDesc('total')
                    ->values()
                    ->toArray();

                return $categoria;
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    protected function agruparGastosPorCategoriasFijas(array $detalle, float $totalBase): array
    {
        $agrupado = collect(self::GASTOS_CATEGORIAS_FIJAS)
            ->mapWithKeys(function ($nombre, $slug) {
                return [
                    $slug => [
                        'nombre' => $nombre,
                        'total' => 0,
                        'subcategorias' => []
                    ]
                ];
            })
            ->toArray();

        foreach ($detalle as $item) {
            $valor = (float) ($item['valor'] ?? 0);
            if ($valor === 0.0) {
                continue;
            }

            $categoriaClave = $this->resolverCategoriaGasto($item['categoria_padre'] ?? $item['subcategoria'] ?? 'Otros Gastos');
            $subcategoria = $item['subcategoria'] ?? ($item['categoria_padre'] ?? 'Sin subcategoría');

            $agrupado[$categoriaClave]['total'] += $valor;
            $agrupado[$categoriaClave]['subcategorias'][$subcategoria] = ($agrupado[$categoriaClave]['subcategorias'][$subcategoria] ?? 0) + $valor;
        }

        return collect($agrupado)->map(function ($categoria) use ($totalBase) {
            $categoria['porcentaje'] = $this->calcularPorcentaje($categoria['total'], $totalBase);
            $categoria['subcategorias'] = collect($categoria['subcategorias'])
                ->map(function ($valor, $nombre) use ($totalBase) {
                    return [
                        'nombre' => $nombre,
                        'total' => $valor,
                        'porcentaje' => $this->calcularPorcentaje($valor, $totalBase)
                    ];
                })
                ->sortByDesc('total')
                ->values()
                ->toArray();

            return $categoria;
        })->values()->toArray();
    }

    protected function resolverCategoriaGasto(?string $nombre): string
    {
        $slug = $this->slugCategoria($nombre);

        foreach (self::GASTOS_CATEGORIAS_FIJAS as $clave => $titulo) {
            if ($slug === $clave) {
                return $clave;
            }
        }

        return 'otros-gastos';
    }

    protected function slugCategoria(?string $texto): string
    {
        if (empty($texto)) {
            return '';
        }

        return Str::slug($texto);
    }

    protected function aplicarPorcentajeSobreFacturacion(array $detalle, float $facturacion): array
    {
        return array_map(function ($item) use ($facturacion) {
            $valor = (float) ($item['valor'] ?? 0);
            $item['porcentaje_facturacion'] = $this->calcularPorcentaje($valor, $facturacion);
            return $item;
        }, $detalle);
    }

    protected function obtenerNominaGastos()
    {
        if ($this->nominaGastos !== null) {
            return $this->nominaGastos;
        }

        $this->nominaGastos = Nempleado::whereYear('fecha_pago', $this->year)
            ->whereMonth('fecha_pago', $this->month)
            ->whereHas('empleado', function ($query) {
                $query->where('tipo', '!=', 1)
                    ->where('cargo', '!=', 5)
                    ->where('tipo_pago', '!=', 'retiro');
            })
            ->sum('total_pagado');

        return $this->nominaGastos;
    }

    protected function obtenerNominaDetallePorTipo(int $tipoClasificado)
    {
        if ($tipoClasificado === 1 && $this->detalleNominaCostos !== null) {
            return $this->detalleNominaCostos;
        }

        if ($tipoClasificado === 2 && $this->detalleNominaGastos !== null) {
            return $this->detalleNominaGastos;
        }

        $detalle = Nempleado::with('empleado')
            ->whereYear('fecha_pago', $this->year)
            ->whereMonth('fecha_pago', $this->month)
            ->whereHas('empleado', function ($query) use ($tipoClasificado) {
                if ($tipoClasificado === 1) {
                    $query->where('tipo', 1)
                        ->where('cargo', '!=', 5)
                        ->where('tipo_pago', '!=', 'retiro');
                } else {
                    $query->where('tipo', '!=', 1)
                        ->where('cargo', '!=', 5)
                        ->where('tipo_pago', '!=', 'retiro');
                }
            })
            ->orderBy('fecha_pago')
            ->get()
            ->map(function ($registro) use ($tipoClasificado) {
                return [
                    'fecha' => $registro->fecha_pago,
                    'descripcion' => 'Nómina de ' . ($registro->empleado->nombre ?? 'Empleado'),
                    'subcategoria' => 'Nómina',
                    'categoria_padre' => $tipoClasificado === 2 ? 'Gastos de Personal' : 'Nómina',
                    'valor' => (float) $registro->total_pagado,
                    'detalle' => $registro->detalle_pago,
                    'fuente' => 'nomina'
                ];
            })
            ->toArray();

        if ($tipoClasificado === 1) {
            $this->detalleNominaCostos = $detalle;
        } else {
            $this->detalleNominaGastos = $detalle;
        }

        return $detalle;
    }

    protected function obtenerCostosDetalle()
    {
        $costos = Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->orderBy('f_costos')
            ->get()
            ->map(function ($costo) {
                return [
                    'fecha' => $costo->f_costos,
                    'descripcion' => $costo->descripcion,
                    'subcategoria' => $this->obtenerNombreSubcategoria($costo->subcategoria),
                    'categoria_padre' => $this->obtenerNombreCategoriaPadre($costo->subcategoria),
                    'valor' => (float) $costo->valor,
                    'fuente' => 'costo'
                ];
            })
            ->toArray();

        return array_merge(
            $costos,
            $this->obtenerDetalleCostosLlaves(),
            $this->obtenerNominaDetallePorTipo(1)
        );
    }

    protected function obtenerGastosDetalle()
    {
        $gastos = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->orderBy('f_gastos')
            ->get()
            ->map(function ($gasto) {
                return [
                    'fecha' => $gasto->f_gastos,
                    'descripcion' => $gasto->descripcion,
                    'subcategoria' => $this->obtenerNombreSubcategoria($gasto->subcategoria),
                    'categoria_padre' => $this->obtenerNombreCategoriaPadre($gasto->subcategoria),
                    'valor' => (float) $gasto->valor,
                    'fuente' => 'gasto'
                ];
            })
            ->toArray();
        // Excluir retiros del dueño: se mostrarán aparte
        return array_merge($gastos, $this->obtenerNominaDetallePorTipo(2));
    }

    protected function obtenerNombreSubcategoria($identificador)
    {
        if (empty($identificador)) {
            return 'Sin subcategoría';
        }

        $info = $this->obtenerRegistroCategoriaCache($identificador);

        return $info['nombre'] ?? (string) $identificador;
    }

    protected function obtenerNombreCategoriaPadre($identificador)
    {
        if (empty($identificador)) {
            return 'Sin categoría';
        }

        $info = $this->obtenerRegistroCategoriaCache($identificador);

        return $info['categoria'] ?? ($info['nombre'] ?? 'Sin categoría');
    }

    protected function obtenerRegistroCategoriaCache($identificador): array
    {
        $clave = is_numeric($identificador) ? 'id_' . $identificador : 'nombre_' . $identificador;

        if (isset($this->categoriaCache[$clave]) && is_array($this->categoriaCache[$clave])) {
            return $this->categoriaCache[$clave];
        }

        if (is_numeric($identificador)) {
            $categoria = Categoria::find($identificador);
        } else {
            $categoria = Categoria::where('nombre', $identificador)->first();
        }

        $this->categoriaCache[$clave] = [
            'nombre' => $categoria?->nombre ?? (string) $identificador,
            'categoria' => $categoria?->categoria ?: ($categoria?->nombre ?? (string) $identificador)
        ];

        return $this->categoriaCache[$clave];
    }

    protected function obtenerRangoMes(): array
    {
        $inicio = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        return [$inicio, $fin];
    }

    protected function obtenerRetirosDuenoTotal()
    {
        if ($this->retiroDuenoTotal !== null) {
            return $this->retiroDuenoTotal;
        }

        [$inicio, $fin] = $this->obtenerRangoMes();

        $this->retiroDuenoTotal = Nempleado::whereHas('empleado', function ($query) {
            $query->where('cargo', 5)->orWhere('tipo_pago', 'retiro');
        })->where(function ($query) use ($inicio, $fin) {
            $query->whereBetween('fecha_pago', [$inicio, $fin])
                ->orWhereBetween('fecha_desde', [$inicio, $fin])
                ->orWhereBetween('fecha_hasta', [$inicio, $fin]);
        })->sum('total_pagado');

        return $this->retiroDuenoTotal;
    }

    protected function obtenerRetirosDuenoDetalle()
    {
        if ($this->detalleRetirosDueno !== null) {
            return $this->detalleRetirosDueno;
        }

        [$inicio, $fin] = $this->obtenerRangoMes();

        $this->detalleRetirosDueno = Nempleado::with('empleado')
            ->whereHas('empleado', function ($query) {
                $query->where('cargo', 5)->orWhere('tipo_pago', 'retiro');
            })
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_pago', [$inicio, $fin])
                    ->orWhereBetween('fecha_desde', [$inicio, $fin])
                    ->orWhereBetween('fecha_hasta', [$inicio, $fin]);
            })
            ->orderBy('fecha_pago')
            ->get()
            ->map(function ($registro) {
                return [
                    'fecha' => $registro->fecha_pago ?? $registro->fecha_desde ?? null,
                    'descripcion' => 'Retiro de ' . ($registro->empleado->nombre ?? 'Dueño'),
                    'subcategoria' => 'Retiros del dueño',
                    'valor' => (float) $registro->total_pagado,
                    'detalle' => $registro->detalle_pago,
                    'fuente' => 'retiro'
                ];
            })
            ->toArray();

        return $this->detalleRetirosDueno;
    }

    protected function obtenerCostosLlavesTotal()
    {
        if ($this->costosLlavesTotal !== null) {
            return $this->costosLlavesTotal;
        }

        [$inicio, $fin] = $this->obtenerRangoMes();
        $ventas = RegistroV::whereBetween('fecha_h', [$inicio, $fin])->get(['items']);

        $total = 0;
        $detalle = [];

        foreach ($ventas as $venta) {
            $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);

            foreach ($items as $item) {
                if (!isset($item['productos']) || !is_array($item['productos'])) {
                    continue;
                }

                foreach ($item['productos'] as $producto) {
                    $cantidad = isset($producto['cantidad']) ? (float) $producto['cantidad'] : 0;
                    $precio = isset($producto['precio']) ? (float) $producto['precio'] : 0;

                    if ($cantidad <= 0 || $precio <= 0) {
                        continue;
                    }

                    $valor = $cantidad * $precio;
                    $nombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                    $clave = $producto['producto'] ?? $nombre;

                    if (!isset($detalle[$clave])) {
                        $detalle[$clave] = [
                            'nombre' => $nombre,
                            'total_cantidad' => 0,
                            'total_valor' => 0
                        ];
                    }

                    $detalle[$clave]['total_cantidad'] += $cantidad;
                    $detalle[$clave]['total_valor'] += $valor;

                    $total += $valor;
                }
            }
        }

        // Incluir ajustes manuales (descargas inventario) en el costo de llaves
        $ajustes = $this->formatearAjustesManuales();
        if (!empty($ajustes['detalle'])) {
            foreach ($ajustes['detalle'] as $ajusteLlave) {
                $claveAjuste = $ajusteLlave['id_producto'] ?? $ajusteLlave['nombre'];
                if (!isset($detalle[$claveAjuste])) {
                    $detalle[$claveAjuste] = [
                        'nombre' => $ajusteLlave['nombre'],
                        'total_cantidad' => 0,
                        'total_valor' => 0
                    ];
                }
                $detalle[$claveAjuste]['total_cantidad'] += $ajusteLlave['total_cantidad'];
                $detalle[$claveAjuste]['total_valor'] += $ajusteLlave['total_valor'];
            }
            $total += $ajustes['total_valor'];
        }

        $this->costosLlavesTotal = $total;
        $this->detalleCostosLlaves = array_values($detalle);

        return $this->costosLlavesTotal;
    }

    protected function obtenerDetalleCostosLlaves()
    {
        $this->obtenerCostosLlavesTotal();

        if (empty($this->detalleCostosLlaves)) {
            return [];
        }

        return array_map(function ($llave) {
            return [
                'fecha' => null,
                'descripcion' => 'Llaves: ' . ($llave['nombre'] ?? 'Sin nombre'),
                'subcategoria' => 'Costo de llaves',
                'categoria_padre' => 'Llaves utilizadas',
                'valor' => (float) ($llave['total_valor'] ?? 0),
                'detalle' => 'Cantidad: ' . number_format($llave['total_cantidad'] ?? 0, 2),
                'fuente' => 'llaves'
            ];
        }, $this->detalleCostosLlaves);
    }

    // Métodos auxiliares
    protected function calcularPorcentaje($valor, $facturacion)
    {
        return $facturacion == 0 ? 0 : ($valor / $facturacion) * 100;
    }

    protected function calcularUtilidadBruta()
    {
        return $this->facturacionDelMes() - $this->totalCostoVenta();
    }

    protected function calcularUtilidadOperativa()
    {
        // Operativa = Bruta - Gastos (sin retiros)
        return $this->calcularUtilidadBruta() - $this->totalGastos();
    }

    protected function calcularUtilidadNeta()
    {
        // Neta = Operativa - Retiros del dueño
        return $this->calcularUtilidadOperativa() - $this->obtenerRetirosDuenoTotal();
    }

    // Método principal que obtiene todas las estadísticas
    protected function getAllStats()
    {
        $facturacion = $this->facturacionDelMes();
        $totalCostoVenta = $this->totalCostoVenta();
        $totalCostosMes = $this->totalCostosDelMes();
        $totalGastos = $this->totalGastos();
        $utilidadBruta = $this->calcularUtilidadBruta();
        $utilidadOperativa = $this->calcularUtilidadOperativa();
        $utilidadNeta = $this->calcularUtilidadNeta();
        $nominaCostos = $this->obtenerNominaCostos();
        $nominaGastos = $this->obtenerNominaGastos();
        $retiroDueno = $this->obtenerRetirosDuenoTotal();
        $costosLlaves = $this->obtenerCostosLlavesTotal();
        $detalleCostos = $this->aplicarPorcentajeSobreFacturacion(
            $this->obtenerCostosDetalle(),
            $facturacion
        );
        $detalleGastos = $this->aplicarPorcentajeSobreFacturacion(
            $this->obtenerGastosDetalle(),
            $facturacion
        );

        // No agregar retiros del dueño a subcategorías de gastos

        // Calcular total de trabajos como en getResumenTrabajos
        $ventas = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get(['items']);
        $trabajos = collect();
        foreach ($ventas as $venta) {
            $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);
            foreach ($items as $item) {
                $trabajoKey = $item['trabajo'] ?? 'Sin especificar';
                if (!$trabajos->has($trabajoKey)) {
                    $trabajos->put($trabajoKey, 0);
                }
                $trabajos->put($trabajoKey, $trabajos->get($trabajoKey) + 1);
            }
        }
        $totalTrabajos = $trabajos->sum();

        // Obtener registros detallados con sus pagos
        $registros = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get();

        // Procesar ventas por trabajo y métodos de pago
        $ventasPorTrabajo = [
            'contado' => [],
            'credito' => []
        ];

        foreach ($registros as $registro) {
            $tipoPago = $registro->tipo_pago ?? 'contado'; // fallback por si no existe
            $trabajos = is_array($registro->items) ? $registro->items : (json_decode($registro->items, true) ?? []);

            foreach ($trabajos as $item) {
                $trabajoKey = is_array($item) ? ($item['trabajo'] ?? (is_string($item) ? $item : 'Sin especificar')) : (is_string($item) ? $item : 'Sin especificar');
                if (!isset($ventasPorTrabajo[$tipoPago][$trabajoKey])) {
                    $ventasPorTrabajo[$tipoPago][$trabajoKey] = [
                        'total' => 0,
                        'metodos' => []
                    ];
                }

                // Procesar métodos de pago
                $pagos = is_array($registro->pagos) ? $registro->pagos : (json_decode($registro->pagos, true) ?? []);
                foreach ($pagos as $pago) {
                    $metodo = $pago['metodo'] ?? ($pago['metodo_pago'] ?? 'N/A'); // Compatibilidad
                    if (!isset($ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo])) {
                        $ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo] = [
                            'total' => 0
                        ];
                    }
                    $ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo]['total'] += $pago['monto'] ?? 0;
                    $ventasPorTrabajo[$tipoPago][$trabajoKey]['total'] += $pago['monto'] ?? 0;
                }
            }
        }

        $ingresosRecibidos = $this->ingresosRecibidosDelMes();
        $ingresosContado = $this->ingresosAlContadoDelMes();
        $cobradoTotal = $ingresosRecibidos + $ingresosContado;

        return [
            // Datos básicos
            'month' => $this->month,
            'year' => $this->year,

            // Estadísticas de ventas
            'ventas' => [
                'cobrado' => $cobradoTotal,
                'ingresos_contado' => $ingresosContado,
                'ingresos_recibidos' => $ingresosRecibidos,
                'facturacion' => $facturacion,
                'evolucion_facturacion' => $this->evolucionFacturacion(),
                'num_transacciones' => $totalTrabajos, // Mostrar la suma de trabajos
                'ticket_promedio' => $this->ticketPromedio(),
            ],
            'ventas_por_trabajo' => $ventasPorTrabajo,

            // Costos y utilidad
            'costos' => [
                'total_costo_venta' => $totalCostoVenta,
                'porcentaje_costo_venta' => $this->calcularPorcentaje($totalCostoVenta, $facturacion),
                'utilidad_bruta' => $utilidadBruta,
                'porcentaje_utilidad_bruta' => $this->calcularPorcentaje($utilidadBruta, $facturacion),
                'total_costos_mes' => $totalCostosMes,
                'porcentaje_total_costos' => $this->calcularPorcentaje($totalCostosMes, $facturacion),
                'nomina_costos' => $nominaCostos,
                'porcentaje_nomina_costos' => $this->calcularPorcentaje($nominaCostos, $facturacion),
                'costos_llaves' => $costosLlaves,
                'porcentaje_costos_llaves' => $this->calcularPorcentaje($costosLlaves, $facturacion),
                'categorias' => $this->agruparDetallePorCategoria($detalleCostos, $facturacion),
                'detalle' => $detalleCostos
            ],
            'gastos' => [
                'categorias' => $this->agruparGastosPorCategoriasFijas($detalleGastos, $facturacion),
                'total_gastos' => $totalGastos,
                'porcentaje_gastos' => $this->calcularPorcentaje($totalGastos, $facturacion),
                'nomina_gastos' => $nominaGastos,
                'porcentaje_nomina_gastos' => $this->calcularPorcentaje($nominaGastos, $facturacion),
                'retiros_dueno' => $retiroDueno,
                'porcentaje_retiros_dueno' => $this->calcularPorcentaje($retiroDueno, $facturacion),
                'detalle' => $detalleGastos
            ],
            // Resultados finales
            'resultados' => [
                'utilidad_operativa' => $utilidadOperativa,
                'porcentaje_utilidad_operativa' => $this->calcularPorcentaje($utilidadOperativa, $facturacion),
                'utilidad_neta' => $utilidadNeta,
                'porcentaje_utilidad_neta' => $this->calcularPorcentaje($utilidadNeta, $facturacion)
            ],
            'llaves' => $this->formatearLlavesStats(),
            'ajustes' => $this->formatearAjustesManuales()
        ];
    }

    protected function obtenerLlavesPorTecnico()
    {
        [$inicio, $fin] = $this->obtenerRangoMes();
        return Empleado::with(['ventas' => function ($query) use ($inicio, $fin) {
            $query->whereBetween('fecha_h', [$inicio, $fin]);
        }])
            ->whereHas('ventas', function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_h', [$inicio, $fin]);
            })
            ->get()
            ->map(function ($tecnico) {
                $llavesInfo = collect();
                $totalLlaves = 0;
                $totalValor = 0;

                foreach ($tecnico->ventas as $venta) {
                    $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);
                    foreach ($items as $item) {
                        if (!isset($item['productos']) || !is_array($item['productos'])) {
                            continue;
                        }
                        foreach ($item['productos'] as $producto) {
                            if (!isset($producto['almacen'], $producto['cantidad'])) {
                                continue;
                            }
                            $llaveNombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                            $almacenId = $producto['almacen'];
                            $cantidad = (float) ($producto['cantidad'] ?? 0);
                            $precio = (float) ($producto['precio'] ?? 0);
                            if ($cantidad <= 0) {
                                continue;
                            }
                            if (!$llavesInfo->has($llaveNombre)) {
                                $llavesInfo->put($llaveNombre, [
                                    'nombre' => $llaveNombre,
                                    'id_producto' => $producto['producto'] ?? null,
                                    'almacenes' => collect(),
                                    'total_cantidad' => 0,
                                    'total_valor' => 0
                                ]);
                            }
                            $llaveData = $llavesInfo->get($llaveNombre);
                            if (!$llaveData['almacenes']->has($almacenId)) {
                                $llaveData['almacenes']->put($almacenId, [
                                    'cantidad' => 0,
                                    'total' => 0
                                ]);
                            }
                            $almacenData = $llaveData['almacenes'][$almacenId];
                            $almacenData['cantidad'] += $cantidad;
                            $almacenData['total'] += ($cantidad * $precio);
                            $llaveData['almacenes'][$almacenId] = $almacenData;
                            $llaveData['total_cantidad'] += $cantidad;
                            $llaveData['total_valor'] += ($cantidad * $precio);
                            $llavesInfo->put($llaveNombre, $llaveData);
                            $totalLlaves += $cantidad;
                            $totalValor += ($cantidad * $precio);
                        }
                    }
                }
                return $totalLlaves > 0 ? [
                    'tecnico' => $tecnico->nombre,
                    'llaves' => $llavesInfo->values()->map(function ($llave) {
                        // Convertir almacenes a array simple
                        $llave['almacenes'] = $llave['almacenes']->map(function ($almacen, $id) {
                            return [
                                'id_almacen' => $id,
                                'cantidad' => $almacen['cantidad'],
                                'total' => $almacen['total']
                            ];
                        })->values();
                        return $llave;
                    }),
                    'total_llaves' => $totalLlaves,
                    'total_valor' => $totalValor
                ] : null;
            })
            ->filter();
    }

    protected function formatearLlavesStats()
    {
        $llavesPorTecnico = $this->obtenerLlavesPorTecnico();
        // Construir resumen global por llave (sin separar por técnico)
        $resumen = collect();
        foreach ($llavesPorTecnico as $tecnico) {
            foreach ($tecnico['llaves'] as $llave) {
                $nombre = $llave['nombre'];
                if (!$resumen->has($nombre)) {
                    $resumen->put($nombre, [
                        'nombre' => $nombre,
                        'total_cantidad' => 0,
                        'total_valor' => 0,
                        'almacenes' => collect()
                    ]);
                }
                $entry = $resumen->get($nombre);
                $entry['total_cantidad'] += $llave['total_cantidad'];
                $entry['total_valor'] += $llave['total_valor'];
                // Acumular almacenes
                foreach ($llave['almacenes'] as $almacen) {
                    $almId = $almacen['id_almacen'];
                    if (!$entry['almacenes']->has($almId)) {
                        $entry['almacenes']->put($almId, [
                            'id_almacen' => $almId,
                            'cantidad' => 0,
                            'total' => 0
                        ]);
                    }
                    $almData = $entry['almacenes']->get($almId);
                    $almData['cantidad'] += $almacen['cantidad'];
                    $almData['total'] += $almacen['total'];
                    $entry['almacenes']->put($almId, $almData);
                }
                $resumen->put($nombre, $entry);
            }
        }
        $resumenLlaves = $resumen->values()->map(function ($llave) {
            $llave['almacenes'] = $llave['almacenes']->values();
            return $llave;
        });
        return [
            'por_tecnico' => $llavesPorTecnico,
            'total_llaves' => $llavesPorTecnico->sum('total_llaves'),
            'total_valor' => $llavesPorTecnico->sum('total_valor'),
            'resumen' => $resumenLlaves
        ];
    }

    protected function obtenerDescargasManualesMes()
    {
        [$inicio, $fin] = $this->obtenerRangoMes();
        return AjusteInventario::with(['producto', 'almacene'])
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_ajuste', [$inicio, $fin])
                    ->orWhere(function ($q) use ($inicio, $fin) {
                        $q->whereNull('fecha_ajuste')->whereBetween('created_at', [$inicio, $fin]);
                    });
            })
            ->where('tipo_ajuste', 'ajuste2')
            ->where('cierre', true)
            ->get()
            ->map(function ($ajuste) {
                $producto = $ajuste->producto;
                $almacen = $ajuste->almacene;
                $precioBase = $ajuste->precio_llave ?? ($producto->precio ?? 0);
                return [
                    'producto' => $producto?->item ?? $producto?->nombre ?? 'Producto no encontrado',
                    'id_producto' => $producto?->id_producto,
                    'almacen' => $almacen?->nombre ?? 'Sin almacén',
                    'id_almacen' => $almacen?->id_almacen,
                    'cantidad_anterior' => $ajuste->cantidad_anterior,
                    'cantidad_nueva' => $ajuste->cantidad_nueva,
                    'diferencia' => abs($ajuste->diferencia),
                    'precio' => (float) $precioBase,
                    'valor' => abs($ajuste->diferencia) * (float)$precioBase
                ];
            });
    }

    protected function formatearAjustesManuales()
    {
        $descargas = $this->obtenerDescargasManualesMes();
        if ($descargas->isEmpty()) {
            return [
                'total_llaves' => 0,
                'total_valor' => 0,
                'detalle' => []
            ];
        }
        $agrupado = $descargas->groupBy('id_producto')->map(function ($grupo) {
            $primero = $grupo->first();
            $totalCantidad = $grupo->sum('diferencia');
            $totalValor = $grupo->sum('valor');
            $almacenes = $grupo->groupBy('id_almacen')->map(function ($sub) {
                return [
                    'id_almacen' => $sub->first()['id_almacen'],
                    'almacen' => $sub->first()['almacen'],
                    'cantidad' => $sub->sum('diferencia'),
                    'valor' => $sub->sum('valor')
                ];
            })->values();
            return [
                'id_producto' => $primero['id_producto'],
                'nombre' => $primero['producto'],
                'total_cantidad' => $totalCantidad,
                'total_valor' => $totalValor,
                'almacenes' => $almacenes
            ];
        })->values();
        return [
            'total_llaves' => $agrupado->sum('total_cantidad'),
            'total_valor' => $agrupado->sum('total_valor'),
            'detalle' => $agrupado
        ];
    }

    //PDF   Que muestra todo
    public function showReportForm()
    {
        // Obtener fechas del mes actual
        $fechaInicio = Carbon::now()->firstOfMonth()->format('Y-m-d');
        $fechaFin = Carbon::now()->lastOfMonth()->format('Y-m-d');

        return view('registrosV.report-form', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
    }

    public function generatePdfTotal(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|numeric|min:2020|max:' . (date('Y') + 1)
        ]);

        $this->month = $request->input('month');
        $this->year = $request->input('year');

        // Obtener todas las estadísticas
        $stats = $this->getAllStats();

        // Obtener registros detallados
        $registros = RegistroV::with('empleado')
            ->whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->orderBy('fecha_h', 'desc')
            ->get();

        // Obtener gastos detallados
        $gastos = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->orderBy('f_gastos', 'desc')
            ->get();

        // Obtener costos detallados
        $costos = Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->orderBy('f_costos', 'desc')
            ->get();

        // Preparar datos para el PDF
        $data = [
            'title' => 'Reporte Estadístico Mensual',
            'date' => now()->format('d/m/Y H:i'),
            'mes' => Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
            'totalTrabajos' => $registros->sum(fn($r) => count(json_decode($r->items, true) ?? [])),
            'totalPagos' => $registros->sum(fn($r) => $r->pagos ? array_sum(array_column($r->pagos, 'monto')) : 0)
        ];

        $pdf = PDF::loadView('estadisticas.stats-pdf', [
            'title' => 'Reporte Financiero',
            'mes' => $this->month . '/' . $this->year,
            'date' => now()->format('d/m/Y H:i'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
        ])->setPaper('a4', 'landscape'); // <-- Esto lo pone horizontal

        return $pdf->stream('reporte_financiero.pdf');
    }
}
