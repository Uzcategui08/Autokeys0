<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Empleado;
use App\Models\TiposDePago;
use App\Models\Costo;
use App\Models\Gasto;
use App\Models\Almacene;
use App\Models\Producto;
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
        $tiposDePago = TiposDePago::all();
        $empleados = Empleado::all()->pluck('nombre', 'id_empleado');

        $reporteVentas = $this->getVentasPorTecnico($startOfWeek, $endOfWeek);
        $reporteCostosGastos = $this->getCostosGastosPorTecnico($startOfWeek, $endOfWeek, $metodosPago);
        $ingresosRecibidos = $this->getIngresosRecibidos($startOfWeek, $endOfWeek, $metodosPago);
        $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startOfWeek, $endOfWeek);

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
                                $llaveId = $producto['producto'] ?? null;
                                $cantidad = (int)$producto['cantidad'];
                                $precio = (float)$producto['precio'];

                                $productoDB = Producto::where('item', $llaveNombre)->first();
                                $llaveId = $productoDB ? $productoDB->id_producto : null;
                                
                                if (!$llavesInfo->has($llaveNombre)) {
                                    $llavesInfo->put($llaveNombre, [
                                        'nombre' => $llaveNombre,
                                        'id_producto' => $llaveId,
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

        $idsAlmacenes = $llavesPorTecnico->flatMap(function($tecnico) {
            return $tecnico['llaves']->flatMap(function($llave) {
                return $llave['almacenes']->keys();
            });
        })->unique()->values();

        $almacenesDisponibles = Almacene::whereIn('id_almacen', $idsAlmacenes)
            ->get()
            ->map(function($almacen) {
                return (object)[
                    'id' => $almacen->id_almacen,
                    'nombre' => $almacen->nombre
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
                'totalGeneralValorLlaves' => $llavesPorTecnico->sum('total_valor'),
                'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
                'tiposDePago' => $tiposDePago,
                'empleados' => $empleados
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
        return RegistroV::selectRaw('EXTRACT(YEAR FROM fecha_h) as year')
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
                },
                'pagosEmpleados' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('fecha_pago', [$startDate, $endDate]);
                }
            ])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereHas('costos', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('f_costos', [$startDate, $endDate]);
                })
                ->orWhereHas('gastos', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('f_gastos', [$startDate, $endDate]);
                })
                ->orWhereHas('pagosEmpleados', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('fecha_pago', [$startDate, $endDate]);
                });
            })
            ->get()
            ->map(function($tecnico) use ($metodosPago) {
                $pagosEmpleado = $tecnico->pagosEmpleados->map(function($pago) use ($tecnico) {
                    $metodoPagoArray = is_string($pago->metodo_pago) ? json_decode($pago->metodo_pago, true) : $pago->metodo_pago;
                    $metodoPagoNombre = is_array($metodoPagoArray) && isset($metodoPagoArray[0]['nombre']) ? $metodoPagoArray[0]['nombre'] : null;

                    return [
                        'valor' => $pago->total_pagado,
                        'metodo_pago' => $metodoPagoNombre,
                        'fecha' => $pago->fecha_pago,
                        'tipo' => $tecnico->tipo
                    ];
                })->toArray();

                $costosEmpleado = array_filter($pagosEmpleado, fn($pago) => $pago['tipo'] == 1);
                $gastosEmpleado = array_filter($pagosEmpleado, fn($pago) => $pago['tipo'] == 2);

                $costosCombinados = $tecnico->costos->toArray();
                $gastosCombinados = $tecnico->gastos->toArray();
                
                if (!empty($costosEmpleado)) {
                    $costosCombinados = array_merge($costosCombinados, $costosEmpleado);
                }
                
                if (!empty($gastosEmpleado)) {
                    $gastosCombinados = array_merge($gastosCombinados, $gastosEmpleado);
                }
                
                return [
                    'tecnico' => $tecnico->nombre,
                    'costos' => $this->procesarTransacciones($costosCombinados, $metodosPago),
                    'gastos' => $this->procesarTransacciones($gastosCombinados, $metodosPago)
                ];
            });
    }

    private function getVentasDetalladasPorTecnico($startDate, $endDate)
    {
        return Empleado::with(['ventas' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate])
                    ->with(['costosAsociados', 'gastosAsociados']);
            }])
            ->whereHas('ventas', function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            })
            ->get()
            ->map(function($tecnico) {
                return [
                    'tecnico' => $tecnico->nombre,
                    'ventas' => $tecnico->ventas->map(function($venta) {
                        $items = json_decode($venta->items, true) ?? [];
                        $pagos = $this->parsePagos($venta->pagos ?? '[]'); 

                        $trabajos = collect($items)->map(function($item) {
                            return [
                                'trabajo' => $this->formatosTrabajo[$item['trabajo'] ?? 'Sin especificar'] ?? ($item['trabajo'] ?? 'Sin especificar'),
                                'precio_trabajo' => $item['precio_trabajo'] ?? 0,
                                'descripcion' => $item['descripcion'] ?? null,
                                'productos' => isset($item['productos']) ? array_map(function($producto) {
                                    return [
                                        'producto' => $producto['producto'] ?? null,
                                        'nombre' => $producto['nombre_producto'] ?? 'Producto sin nombre',
                                        'cantidad' => $producto['cantidad'] ?? 0,
                                        'precio' => $producto['precio'] ?? 0,
                                        'almacen' => $producto['almacen'] ?? null
                                    ];
                                }, $item['productos']) : []
                            ];
                        });

                        $costos = $venta->costosAsociados->map(function($costo) {
                            $pagosCosto = $this->parsePagos($costo->pagos ?? '[]');
                            return [
                                'id' => $costo->id_costos,
                                'descripcion' => $costo->descripcion,
                                'subcategoria' => $this->formatearSubcategoria($costo->subcategoria),
                                'valor' => $costo->valor,
                                'metodo_pago_id' => $pagosCosto[0]['metodo_pago'] ?? null,
                                'metodos_pago' => collect($pagosCosto)->pluck('metodo_pago')->unique()->implode(', '),
                                'fecha' => $costo->f_costos
                            ];
                        });

                        $gastos = $venta->gastosAsociados->map(function($gasto) {
                            $pagosGasto = $this->parsePagos($gasto->pagos ?? '[]');
                            return [
                                'id' => $gasto->id_gastos,
                                'descripcion' => $gasto->descripcion,
                                'subcategoria' => $this->formatearSubcategoria($gasto->subcategoria),
                                'valor' => $gasto->valor,
                                'metodo_pago_id' => $pagosGasto[0]['metodo_pago'] ?? null,
                                'metodos_pago' => collect($pagosGasto)->pluck('metodo_pago')->unique()->implode(', '),
                                'fecha' => $gasto->f_gastos
                            ];
                        });

                        $metodosPago = collect($pagos)->pluck('metodo_pago')->unique()->implode(', ');
                        $totalPagado = collect($pagos)->sum('monto');
                        
                        return [
                            'id' => $venta->id,
                            'fecha' => $venta->fecha_h,
                            'cliente' => $venta->cliente,
                            'valor_total' => $venta->valor_v,
                            'tipo_venta' => $venta->tipo_venta,
                            'estatus' => $venta->estatus,
                            'pagos' => $pagos,
                            'metodos_pago' => $metodosPago,
                            'total_pagado' => $totalPagado,
                            'trabajos' => $trabajos,
                            'costos' => $costos,
                            'total_costos' => $costos->sum('valor'),
                            'gastos' => $gastos,
                            'total_gastos' => $gastos->sum('valor'),
                            'ganancia_bruta' => $venta->valor_v - $costos->sum('valor') - $gastos->sum('valor')
                        ];
                    }),
                    'total_ventas' => $tecnico->ventas->sum('valor_v'),
                    'total_costos' => $tecnico->ventas->sum(function($venta) {
                        return $venta->costosAsociados->sum('valor');
                    }),
                    'total_gastos' => $tecnico->ventas->sum(function($venta) {
                        return $venta->gastosAsociados->sum('valor');
                    }),
                    'ganancia_total' => $tecnico->ventas->sum('valor_v') - 
                                    $tecnico->ventas->sum(function($venta) {
                                        return $venta->costosAsociados->sum('valor') + 
                                                $venta->gastosAsociados->sum('valor');
                                    })
                ];
            });
    }

    private function getIngresosRecibidos($startDate, $endDate, $metodosPago)
{
    return Empleado::with(['ventas'])
        ->whereHas('ventas', function($query) {
            $query->whereNotNull('pagos')
                  ->whereRaw("json_array_length(pagos) > 0"); // PostgreSQL-compatible check
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
            ->get(['id', 'tipo_venta', 'valor_v', 'pagos', 'items']);
        
        $contado = collect();
        $credito = collect();
        
        foreach ($ventas as $venta) {
            $items = json_decode($venta->items, true) ?? [];
            $pagosVenta = $this->parsePagos($venta->pagos);

            $totalItems = count($items);
            $valorPorItem = $totalItems > 0 ? $venta->valor_v / $totalItems : $venta->valor_v;
            
            foreach ($items as $item) {
                $trabajoKey = $item['trabajo'] ?? 'Sin especificar';
                $trabajoNombre = $this->formatosTrabajo[$trabajoKey] ?? $trabajoKey;
                
                if ($venta->tipo_venta === 'contado') {
                    if (!$contado->has($trabajoNombre)) {
                        $contado->put($trabajoNombre, [
                            'metodos' => collect(),
                            'total' => 0
                        ]);
                    }
                    
                    $trabajoData = $contado->get($trabajoNombre);
                    $trabajoData['total'] += $valorPorItem;

                    foreach ($pagosVenta as $pago) {
                        $metodoNombre = $metodosPago[$pago['metodo_pago']] ?? 'Método '.$pago['metodo_pago'];
                        $montoProporcional = $pago['monto'] / $totalItems;
                        
                        if (!$trabajoData['metodos']->has($metodoNombre)) {
                            $trabajoData['metodos']->put($metodoNombre, [
                                'total' => 0,
                                'count' => 0,
                                'metodo_id' => $pago['metodo_pago'] ?? null
                            ]);
                        }
                        
                        $metodoData = $trabajoData['metodos']->get($metodoNombre);
                        $metodoData['total'] += $montoProporcional;
                        $metodoData['count'] += 1;
                        $trabajoData['metodos']->put($metodoNombre, $metodoData);
                    }
                    
                    $contado->put($trabajoNombre, $trabajoData);
                } else {
                    if (!$credito->has($trabajoNombre)) {
                        $credito->put($trabajoNombre, [
                            'metodos' => collect(),
                            'total' => 0
                        ]);
                    }
                    
                    $trabajoData = $credito->get($trabajoNombre);
                    $trabajoData['total'] += $valorPorItem;

                    foreach ($pagosVenta as $pago) {
                        $metodoNombre = $metodosPago[$pago['metodo_pago']] ?? 'Método '.$pago['metodo_pago'];
                        $montoProporcional = $pago['monto'] / $totalItems;
                        
                        if (!$trabajoData['metodos']->has($metodoNombre)) {
                            $trabajoData['metodos']->put($metodoNombre, [
                                'total' => 0,
                                'count' => 0,
                                'metodo_id' => $pago['metodo_pago'] ?? null
                            ]);
                        }
                        
                        $metodoData = $trabajoData['metodos']->get($metodoNombre);
                        $metodoData['total'] += $montoProporcional;
                        $metodoData['count'] += 1;
                        $trabajoData['metodos']->put($metodoNombre, $metodoData);
                    }
                    
                    $credito->put($trabajoNombre, $trabajoData);
                }
            }
        }
        
        return [
            'contado' => $contado->sortByDesc('total'),
            'credito' => $credito->sortByDesc('total'),
            'total_contado' => $contado->sum('total'),
            'total_credito' => $credito->sum('total')
        ];
    }

    private function getResumenTrabajos($startDate, $endDate)
    {
        $ventas = RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['items']);

        $trabajos = collect();

        foreach ($ventas as $venta) {
            $items = json_decode($venta->items, true) ?? [];
            
            foreach ($items as $item) {
                $trabajoKey = $item['trabajo'] ?? 'Sin especificar';
                
                if (!$trabajos->has($trabajoKey)) {
                    $trabajos->put($trabajoKey, 0);
                }
                
                $trabajos->put($trabajoKey, $trabajos->get($trabajoKey) + 1);
            }
        }

        return $trabajos->map(function ($cantidad, $trabajoKey) {
            return [
                'cantidad' => $cantidad,
                'nombre' => $this->formatosTrabajo[$trabajoKey] ?? $trabajoKey
            ];
        })->sortByDesc('cantidad')->values();
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
        if (!is_array($transacciones)) {
            $transacciones = $transacciones->toArray();
        }

        $resultados = [];
        
        foreach ($transacciones as $transaccion) {
            if (is_array($transaccion) && isset($transaccion['tipo'])) {
                $metodoPagoKey = $transaccion['metodo_pago'] ?? null;
                $resultados[] = [
                    'subcategoria' => $transaccion['tipo'] == 1 ? 'Salario Cerrajero' : 'Gastos Personal',
                    'descripcion' => 'Pago a empleado',
                    'metodo_pago' => $metodosPago[$metodoPagoKey] ?? ucfirst($metodoPagoKey) ?? 'Desconocido',
                    'total' => $transaccion['valor'],
                    'fecha_pago' => $transaccion['fecha']
                ];
            } elseif (is_array($transaccion)) {
                $pagos = isset($transaccion['pagos']) ? 
                    (is_array($transaccion['pagos']) ? $transaccion['pagos'] : json_decode($transaccion['pagos'], true) ?? []) 
                    : [];
                foreach ($pagos as $pago) {
                    $metodoPagoKey = $pago['metodo_pago'] ?? null;
                    $resultados[] = [
                        'subcategoria' => $this->formatearSubcategoria($transaccion['subcategoria'] ?? ''),
                        'descripcion' => $transaccion['descripcion'] ?? '',
                        'metodo_pago' => $metodosPago[$metodoPagoKey] ?? ucfirst($metodoPagoKey) ?? 'Desconocido',
                        'total' => $pago['monto'] ?? 0,
                        'fecha_pago' => $pago['fecha'] ?? null
                    ];
                }
            }
        }

        $agrupados = [];
        foreach ($resultados as $item) {
            $key = $item['subcategoria'].'|'.$item['descripcion'];
            
            if (!isset($agrupados[$key])) {
                $agrupados[$key] = [
                    'subcategoria' => $item['subcategoria'],
                    'descripcion' => $item['descripcion'],
                    'metodos_pago' => [],
                    'total' => 0,
                    'fecha_pago' => $item['fecha_pago']
                ];
            }
            
            if (!in_array($item['metodo_pago'], $agrupados[$key]['metodos_pago'])) {
                $agrupados[$key]['metodos_pago'][] = $item['metodo_pago'];
            }
            
            $agrupados[$key]['total'] += $item['total'];
        }

        foreach ($agrupados as &$item) {
            $item['metodo_pago'] = implode(', ', $item['metodos_pago']);
            unset($item['metodos_pago']);
        }
        unset($item);

        usort($agrupados, function($a, $b) {
            return strcmp($a['subcategoria'], $b['subcategoria']);
        });

        return array_values($agrupados);
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