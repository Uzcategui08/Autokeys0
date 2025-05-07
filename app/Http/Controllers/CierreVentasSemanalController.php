<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Empleado;
use App\Models\TiposDePago;
use App\Models\Costo;
use App\Models\Gasto;
use App\Models\Almacene;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CierreVentasSemanalController extends Controller
{
    private $formatosTrabajo = [
        'duplicado' => 'Duplicado',
        'perdida' => 'Pérdida',
        'programacion' => 'Programación',
        'alarma' => 'Alarma',
        'airbag' => 'Airbag',
        'rekey' => 'Rekey',
        'lishi' => 'Lishi',
        'remote_start' => 'Remote Start',
        'control' => 'Control',
        'venta' => 'Venta',
        'apertura' => 'Apertura',
        'cambio_chip' => 'Cambio de Chip',
        'revision' => 'Revisión',
        'suiche' => 'Suiche',
        'llave_puerta' => 'Llave de Puerta',
        'cinturon' => 'Cinturón',
        'diag' => 'Diagnóstico',
        'emuladores' => 'Emuladores',
        'clonacion' => 'Clonación'
    ];

    public function index(Request $request)
    {
        $yearSelected = $request->input('year', now()->year);
        $weekSelected = $request->input('week', now()->weekOfYear);

        if ($request->has('start_date') && $request->has('end_date')) {
            try {
                $startOfWeek = Carbon::parse($request->input('start_date'))->startOfDay();
                $endOfWeek = Carbon::parse($request->input('end_date'))->endOfDay();

                if ($startOfWeek->diffInDays($endOfWeek) > 31) {
                    throw new \Exception("Rango máximo excedido");
                }

                $weekSelected = $startOfWeek->weekOfYear;
                $yearSelected = $startOfWeek->year;
                
            } catch (\Exception $e) {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
            }
        } else {
            $startOfWeek = Carbon::now()->setISODate($yearSelected, $weekSelected)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
        }

        $availableYears = $this->getAvailableYears();
        $metodosPago = TiposDePago::pluck('name', 'id');

        $reporteVentas = $this->getVentasPorTecnico($startOfWeek, $endOfWeek);
        $reporteCostosGastos = $this->getCostosGastosPorTecnico($startOfWeek, $endOfWeek, $metodosPago);
        $ingresosRecibidos = $this->getIngresosRecibidos($startOfWeek, $endOfWeek, $metodosPago);

        $llavesPorTecnico = Empleado::with(['ventas' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('fecha_h', [$startOfWeek, $endOfWeek]);
        }])
        ->whereHas('ventas', function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('fecha_h', [$startOfWeek, $endOfWeek]);
        })
        ->get()
        ->map(function($tecnico) {
            $llavesInfo = collect();
            $totalLlaves = 0;
            $totalValor = 0;
            
            foreach ($tecnico->ventas as $venta) {
                $items = json_decode($venta->items, true) ?? [];
                
                foreach ($items as $item) {
                    if (isset($item['productos']) && is_array($item['productos'])) {
                        foreach ($item['productos'] as $producto) {
                            if (isset($producto['almacen'], $producto['cantidad'], $producto['precio'])) {
                                $almacenId = $producto['almacen'];
                                $llaveNombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                                $cantidad = (int)$producto['cantidad'];
                                $precio = (float)$producto['precio'];
                                
                                if (!$llavesInfo->has($llaveNombre)) {
                                    $llavesInfo->put($llaveNombre, [
                                        'nombre' => $llaveNombre,
                                        'almacenes' => collect(),
                                        'total_cantidad' => 0,
                                        'total_valor' => 0
                                    ]);
                                }
                                
                                $llave = $llavesInfo->get($llaveNombre);
                                
                                if (!$llave['almacenes']->has($almacenId)) {
                                    $llave['almacenes']->put($almacenId, [
                                        'cantidad' => 0,
                                        'total' => 0
                                    ]);
                                }
                                
                                $almacen = $llave['almacenes']->get($almacenId);
                                $almacen['cantidad'] += $cantidad;
                                $almacen['total'] += ($cantidad * $precio);
                                $llave['almacenes']->put($almacenId, $almacen);
                                
                                $llave['total_cantidad'] += $cantidad;
                                $llave['total_valor'] += ($cantidad * $precio);
                                $llavesInfo->put($llaveNombre, $llave);
                                
                                $totalLlaves += $cantidad;
                                $totalValor += ($cantidad * $precio);
                            }
                        }
                    }
                }
            }
    
            return $totalLlaves > 0 ? [
                'tecnico' => $tecnico->nombre,
                'llaves' => $llavesInfo->values(),
                'total_llaves' => $totalLlaves,
                'total_valor' => $totalValor
            ] : null;
        })
        ->filter();

        $almacenesDisponibles = $llavesPorTecnico->flatMap(function($tecnico) {
            return $tecnico['llaves']->flatMap(function($llave) {
                return $llave['almacenes']->keys();
            });
        })
        ->unique()
        ->map(function($id) {
            return (object)[
                'id' => $id,
                'nombre' => 'Almacén '.$id
            ];
        });

        $ventasPorCliente = $this->getVentasPorCliente($startOfWeek, $endOfWeek);
        $ventasPorTrabajo = $this->getVentasPorTrabajo($startOfWeek, $endOfWeek, $metodosPago);
        $resumenTrabajos = $this->getResumenTrabajos($startOfWeek, $endOfWeek);
        $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startOfWeek, $endOfWeek);

        $totales = $this->calcularTotales(
            $reporteVentas, 
            $reporteCostosGastos, 
            $ingresosRecibidos,
            $llavesPorTecnico
        );
        
        $totalCostosLlaves = $llavesPorTecnico->sum('total_valor');
        
        $ganancia = ($totales['totalVentas'] ?? 0) 
                  - ($totales['totalCostos'] ?? 0) 
                  - ($totalCostosLlaves ?? 0) 
                  - ($totales['totalGastos'] ?? 0);

        return view('estadisticas.cierre-semanal', array_merge(
            [
                'weekSelected' => $weekSelected,
                'yearSelected' => $yearSelected,
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
                'availableYears' => $availableYears,
                'metodosPago' => $metodosPago,
                'reporteVentas' => $reporteVentas,
                'reporteCostosGastos' => $reporteCostosGastos,
                'ingresosRecibidos' => $ingresosRecibidos,
                'llavesPorTecnico' => $llavesPorTecnico,
                'almacenesDisponibles' => $almacenesDisponibles,
                'ventasPorCliente' => $ventasPorCliente,
                'totalVentasClientes' => $ventasPorCliente->sum('total_ventas'),
                'ventasPorTrabajo' => $ventasPorTrabajo,
                'totalCostosLlaves' => $totalCostosLlaves,
                'ganancia' => $ganancia,
                'resumenTrabajos' => $resumenTrabajos,
                'totalTrabajos' => $resumenTrabajos->sum('cantidad'),
                'ventasPorLugarVenta' => $ventasPorLugarVenta,
                'totalGeneralLlaves' => $llavesPorTecnico->sum('total_llaves'),
                'totalGeneralValorLlaves' => $llavesPorTecnico->sum('total_valor')
            ],
            $totales
        ));
    }

    private function getWeeksWithDates($year)
    {
        $weeks = [];
        $date = Carbon::create($year, 1, 1)->startOfWeek();

        if ($date->weekOfYear > 1) {
            $date->subWeek();
        }
        
        for ($i = 1; $i <= 52; $i++) {
            $start = $date->copy();
            $end = $date->copy()->endOfWeek();
            
            $weeks[$i] = [
                'number' => $i,
                'start' => $start->format('d M'),
                'end' => $end->format('d M'),
                'full' => $start->format('d M') . ' - ' . $end->format('d M Y')
            ];
            
            $date->addWeek();
        }
        
        return $weeks;
    }

    private function getAvailableYears()
    {
        return RegistroV::selectRaw('YEAR(fecha_h) as year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year');
    }

    private function getVentasPorTecnico($startDate, $endDate)
    {
        return Empleado::with(['ventas' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            }])
            ->whereHas('ventas', function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            })
            ->get()
            ->map(function($tecnico) {
                $ventasContado = $tecnico->ventas->where('tipo_venta', 'contado')->sum('valor_v');
                $ventasCredito = $tecnico->ventas->where('tipo_venta', 'credito')->sum('valor_v');
                
                return [
                    'tecnico' => $tecnico->nombre,
                    'ventas_contado' => $ventasContado,
                    'ventas_credito' => $ventasCredito,
                    'total_ventas' => $ventasContado + $ventasCredito
                ];
            });
    }

    private function getCostosGastosPorTecnico($startDate, $endDate, $metodosPago)
    {
        return Empleado::with([
                'costos' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('f_costos', [$startDate, $endDate]);
                },
                'gastos' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('f_gastos', [$startDate, $endDate]);
                }
            ])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereHas('costos', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('f_costos', [$startDate, $endDate]);
                })
                ->orWhereHas('gastos', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('f_gastos', [$startDate, $endDate]);
                });
            })
            ->get()
            ->map(function($tecnico) use ($metodosPago) {
                return [
                    'tecnico' => $tecnico->nombre,
                    'costos' => $this->procesarTransacciones($tecnico->costos, $metodosPago),
                    'gastos' => $this->procesarTransacciones($tecnico->gastos, $metodosPago)
                ];
            });
    }

    private function getIngresosRecibidos($startDate, $endDate, $metodosPago)
    {
        return Empleado::with(['ventas'])
            ->whereHas('ventas', function($query) {
                $query->whereNotNull('pagos')
                      ->where('pagos', '!=', '[]');
            })
            ->get()
            ->map(function($tecnico) use ($startDate, $endDate, $metodosPago) {
                $pagosRecibidos = collect();
                
                foreach ($tecnico->ventas as $venta) {
                    $pagos = $this->parsePagos($venta->pagos);
                    
                    foreach ($pagos as $pago) {
                        if (!isset($pago['fecha'], $pago['metodo_pago'], $pago['monto'])) {
                            continue;
                        }
                        
                        $fechaPago = Carbon::parse($pago['fecha']);
                        $fechaVenta = Carbon::parse($venta->fecha_h);
                        
                        if ($fechaPago->between($startDate, $endDate) && !$fechaVenta->between($startDate, $endDate)) {
                            $pagosRecibidos->push([
                                'metodo_pago' => $metodosPago[$pago['metodo_pago']] ?? 'Desconocido',
                                'monto' => $pago['monto'],
                                'fecha_venta' => $venta->fecha_h,
                                'fecha_pago' => $pago['fecha']
                            ]);
                        }
                    }
                }
        
                return [
                    'tecnico' => $tecnico->nombre,
                    'pagos' => $pagosRecibidos,
                    'total' => $pagosRecibidos->sum('monto')
                ];
            });
    }

    private function getVentasPorCliente($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get()
            ->groupBy('cliente') 
            ->map(function ($ventas, $nombreCliente) {
                return [
                    'cliente' => $nombreCliente,
                    'ventas_contado' => $ventas->where('tipo_venta', 'contado')->sum('valor_v'),
                    'ventas_credito' => $ventas->where('tipo_venta', 'credito')->sum('valor_v'),
                    'total_ventas' => $ventas->sum('valor_v')
                ];
            })
            ->values()
            ->sortByDesc('total_ventas');
    }

    private function getVentasPorTrabajo($startDate, $endDate, $metodosPago)
    {
        $ventas = RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['id', 'trabajo', 'tipo_venta', 'valor_v', 'pagos']);
        
        $procesarVentas = function ($ventasGrupo) use ($metodosPago) {
            $pagos = collect();
            
            foreach ($ventasGrupo as $venta) {
                $pagosVenta = $this->parsePagos($venta->pagos);
                
                foreach ($pagosVenta as $pago) {
                    if (!isset($pago['metodo_pago'], $pago['monto'])) {
                        continue;
                    }
                    
                    $metodoNombre = $metodosPago[$pago['metodo_pago']] ?? 'Método '.$pago['metodo_pago'];
                    $monto = (float)$pago['monto'];
                    
                    $pagos->push([
                        'metodo' => $metodoNombre,
                        'monto' => $monto,
                        'metodo_id' => $pago['metodo_pago']
                    ]);
                }
            }
            
            return $pagos->groupBy('metodo')
                ->map(function ($grupo) {
                    return [
                        'total' => $grupo->sum('monto'),
                        'count' => $grupo->count(),
                        'metodo_id' => $grupo->first()['metodo_id'] ?? null
                    ];
                })
                ->sortByDesc('total');
        };
        
        $contado = $ventas->where('tipo_venta', 'contado')
            ->groupBy('trabajo')
            ->map(function ($ventasGrupo, $trabajoKey) use ($procesarVentas) {
                $metodos = $procesarVentas($ventasGrupo);
                
                return [
                    'metodos' => $metodos,
                    'total' => $metodos->sum('total'),
                    'trabajo_key' => $trabajoKey
                ];
            })
            ->sortByDesc('total')
            ->mapWithKeys(function ($item, $trabajoKey) {
                $nombreFormateado = $this->formatosTrabajo[$trabajoKey] ?? $trabajoKey;
                return [$nombreFormateado => [
                    'metodos' => $item['metodos'],
                    'total' => $item['total']
                ]];
            });
    
        $credito = $ventas->where('tipo_venta', 'credito')
            ->groupBy('trabajo')
            ->map(function ($ventasGrupo, $trabajoKey) use ($procesarVentas) {
                $metodos = $procesarVentas($ventasGrupo);
                
                return [
                    'metodos' => $metodos,
                    'total' => $metodos->sum('total'),
                    'trabajo_key' => $trabajoKey 
                ];
            })
            ->sortByDesc('total')
            ->mapWithKeys(function ($item, $trabajoKey) {
                $nombreFormateado = $this->formatosTrabajo[$trabajoKey] ?? $trabajoKey;
                return [$nombreFormateado => [
                    'metodos' => $item['metodos'],
                    'total' => $item['total']
                ]];
            });
        
        return [
            'contado' => $contado,
            'credito' => $credito,
            'total_contado' => $contado->sum('total'),
            'total_credito' => $credito->sum('total')
        ];
    }

    private function getResumenTrabajos($startDate, $endDate)
    {
        $ventas = RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->select('trabajo')
            ->get();

        return $ventas->groupBy('trabajo')
            ->map(function ($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'nombre' => $this->formatosTrabajo[$grupo->first()->trabajo] ?? $grupo->first()->trabajo
                ];
            })
            ->sortByDesc('cantidad');
    }

    private function getVentasPorLugarVenta($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['id', 'lugarventa', 'valor_v'])
            ->groupBy('lugarventa')
            ->map(function($ventas, $lugarVenta) {
                return [
                    'nombre' => $lugarVenta ?? 'Sin especificar',
                    'cantidad' => $ventas->count(),
                    'monto' => $ventas->sum('valor_v')
                ];
            });
    }

    private function parsePagos($pagosData)
    {
        if (is_string($pagosData)) {
            return json_decode($pagosData, true) ?? [];
        }
        
        if (is_array($pagosData)) {
            return $pagosData;
        }
        
        return [];
    }

    private function procesarTransacciones($transacciones, $metodosPago)
    {
        return $transacciones
            ->flatMap(function($transaccion) use ($metodosPago) {
                $pagos = is_array($transaccion->pagos) 
                       ? $transaccion->pagos 
                       : json_decode($transaccion->pagos, true) ?? [];
                
                return collect($pagos)->map(function($pago) use ($transaccion, $metodosPago) {
                    return [
                        'subcategoria' => $this->formatearSubcategoria($transaccion->subcategoria),
                        'metodo_pago' => $metodosPago[$pago['metodo_pago']] ?? 'Desconocido',
                        'total' => $pago['monto'],
                        'fecha_pago' => $pago['fecha'] ?? null
                    ];
                });
            })
            ->groupBy(['subcategoria', 'metodo_pago'])
            ->map(function($group) {
                return [
                    'subcategoria' => $group->first()->first()['subcategoria'],
                    'metodo_pago' => $group->first()->first()['metodo_pago'],
                    'total' => $group->flatten(1)->sum('total'),
                    'fecha_pago' => $group->first()->first()['fecha_pago']
                ];
            })
            ->values()
            ->sortBy('subcategoria');
    }

    private function calcularTotales($reporteVentas, $reporteCostosGastos, $ingresosRecibidos, $llavesData)
    {
        return [
            'totalVentasContado' => collect($reporteVentas)->sum('ventas_contado'),
            'totalVentasCredito' => collect($reporteVentas)->sum('ventas_credito'),
            'totalVentas' => collect($reporteVentas)->sum('total_ventas'),
            'totalCostos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['costos'])->sum('total')),
            'totalGastos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['gastos'])->sum('total')),
            'totalIngresosRecibidos' => $ingresosRecibidos->sum('total'),
            'totalGeneralLlaves' => $llavesData->sum('total_llaves'),
            'totalGeneralValorLlaves' => $llavesData->sum('total_valor')
        ];
    }

    private function formatearSubcategoria($subcategoria)
    {
        $formateadas = [
            'compras_insumos' => 'Compras Insumos',
            'gasolina' => 'Gasolina',
            'mantenimiento_vanes' => 'Mantenimiento Vanes',
            'salario_cerrajero' => 'Salario Cerrajero',
            'depreciacion_maquinas' => 'Depreciación Máquinas',
            'seguros_vehiculos' => 'Seguros Vehículos',
            'alquiler_pulga' => 'Alquiler Pulga',
            'codigos' => 'Códigos',
            'servicios_subcontratados' => 'Servicios Subcontratados'
        ];

        return $formateadas[$subcategoria] ?? ucfirst(str_replace('_', ' ', $subcategoria));
    }
}