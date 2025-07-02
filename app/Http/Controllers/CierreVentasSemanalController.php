<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Empleado;
use App\Models\TiposDePago;
use App\Models\Costo;
use App\Models\Gasto;
use App\Models\Almacene;
use App\Models\Producto;
use App\Models\AjusteInventario;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CierreSemanalExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Nempleado;
use Illuminate\Support\Facades\Log;

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

        $llavesPorTecnico = $this->getLlavesPorTecnico($startOfWeek, $endOfWeek);

        $descargasManuales = $this->getCargasDescargas($startOfWeek, $endOfWeek);

        $descargasManualesFormato = [
            'tecnico' => 'Manual',
            'llaves' => collect($descargasManuales)->groupBy('producto')->map(function($grupo, $producto) {
                $primerRegistro = $grupo->first();
                $idProducto = $primerRegistro['id_producto'] ?? null;
                
                $llave = [
                    'nombre' => Producto::where('id_producto', $primerRegistro['id_producto'])->value('item') ?? 'Producto no encontrado',
                    'id_producto' => $idProducto,
                    'almacenes' => collect($grupo)->groupBy('id_almacen')->map(function($almacenGrupo) use ($idProducto) {
                        $diferencias = $almacenGrupo->map(function($item) {
                            return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                        });
                        
                        $cantidad = $diferencias->sum();

                        $precio = Producto::where('id_producto', $almacenGrupo->first()['id_producto'])->value('precio') ?? 0;
                        return [
                            'cantidad' => $cantidad,
                            'total' => $cantidad * $precio,
                            'id_producto' => $idProducto
                        ];
                    }),
                    'total_cantidad' => $grupo->map(function($item) {
                        return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                    })->sum(),
                    'total_valor' => $grupo->map(function($item) {
                        $diferencia = abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                        $precio = Producto::where('id_producto', $item['id_producto'])->value('precio') ?? 0;
                        return $diferencia * $precio;
                    })->sum(),
                    'id_producto' => $idProducto
                ];
                return $llave;
            }),
            'total_llaves' => $descargasManuales->map(function($item) {
                return $item['cantidad'];
            })->sum(),
            'total_valor' => $descargasManuales->map(function($item) {
                $diferencia = abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                return $diferencia * ($item['precio'] ?? 0);
            })->sum()
        ];

        $llavesPorTecnico = collect($llavesPorTecnico);
        $llavesPorTecnico->push($descargasManualesFormato);

        $idsAlmacenes = collect([]);
        foreach ($llavesPorTecnico as $tecnico) {
            foreach ($tecnico['llaves'] as $llave) {
                foreach ($llave['almacenes'] as $almacenId => $datos) {
                    if (is_numeric($almacenId)) {
                        $idsAlmacenes->push((int)$almacenId);
                    }
                }
            }
        }
        $idsAlmacenes = $idsAlmacenes->unique()->values();

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
        $cargasDescargas = $this->getCargasDescargas($startOfWeek, $endOfWeek);

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

        $retiroDueño = Nempleado::whereHas('empleado', function($query) {
            $query->where('cargo', 5); 
        })
        ->whereBetween('fecha_pago', [$startOfWeek, $endOfWeek])
        ->sum('total_pagado');

        $gananciaFinal = $ganancia - $retiroDueño;

        $cargasDescargas = $descargasManuales;

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
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal,
                'resumenTrabajos' => $resumenTrabajos,
                'totalTrabajos' => $resumenTrabajos->sum('cantidad'),
                'ventasPorLugarVenta' => $ventasPorLugarVenta,
                'totalGeneralLlaves' => $llavesPorTecnico->sum('total_llaves'),
                'totalGeneralValorLlaves' => $llavesPorTecnico->sum('total_valor'),
                'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
                'tiposDePago' => $tiposDePago,
                'empleados' => $empleados,
                'cargasDescargas' => $cargasDescargas,
                'totalCargas' => $cargasDescargas->where('es_carga', true)->sum('cantidad'),
                'totalDescargas' => $cargasDescargas->where('tipo', 'ajuste2')->sum('cantidad'),
            ],
            $totales
        ));
    }

    public function exportPdf(Request $request)
    {
        try {
            Log::info('Inicio de exportPdf');
            Log::info('Datos del request:', [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'year' => $request->input('year'),
                'week' => $request->input('week')
            ]);

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if (!$startDate || !$endDate) {
                Log::info('Usando fechas por semana');
                $startDate = Carbon::parse($request->input('year', now()->year) . 'W' . $request->input('week', now()->weekOfYear))->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
            } else {
                Log::info('Usando fechas seleccionadas');
                $startDate = Carbon::parse($startDate);
                $endDate = Carbon::parse($endDate);
            }

            Log::info('Fechas calculadas:', [
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d')
            ]);

            // Logging for database queries
            Log::info('Consultando métodos de pago');
            $metodosPago = TiposDePago::all();
            Log::info('Métodos de pago encontrados:', [
                'count' => $metodosPago->count(),
                'data' => $metodosPago->map(function($pago) {
                    return [
                        'id' => $pago->id,
                        'name' => $pago->name
                    ];
                })->toArray()
            ]);

            Log::info('Consultando reporte de ventas');
            $reporteVentas = $this->getVentasPorTecnico($startDate, $endDate);
            Log::info('Ventas encontradas:', [
                'count' => count($reporteVentas),
                'first' => $reporteVentas->first() ?? null
            ]);

            Log::info('Consultando costos y gastos');
            $reporteCostosGastos = $this->getCostosGastosPorTecnico($startDate, $endDate, $metodosPago);
            Log::info('Costos y gastos encontrados:', [
                'count' => count($reporteCostosGastos),
                'first' => $reporteCostosGastos->first() ?? null
            ]);

            Log::info('Consultando ingresos recibidos');
            $ingresosRecibidos = $this->getIngresosRecibidos($startDate, $endDate, $metodosPago);
            Log::info('Ingresos encontrados:', [
                'count' => count($ingresosRecibidos),
                'first' => $ingresosRecibidos->first() ?? null
            ]);

            Log::info('Consultando llaves por técnico');
            $llavesPorTecnico = $this->getLlavesPorTecnico($startDate, $endDate);
            Log::info('Llaves encontradas:', [
                'count' => count($llavesPorTecnico),
                'first' => $llavesPorTecnico->first() ?? null
            ]);

            Log::info('Consultando descargas manuales');
            $descargasManuales = $this->getCargasDescargas($startDate, $endDate);
            Log::info('Descargas encontradas:', [
                'count' => count($descargasManuales),
                'first' => $descargasManuales->first() ?? null
            ]);

            Log::info('Calculando totales');
            $totales = $this->calcularTotales(
                $reporteVentas, 
                $reporteCostosGastos, 
                $ingresosRecibidos,
                $llavesPorTecnico
            );
            Log::info('Totales calculados:', $totales);
            
            Log::info('Calculando costos de llaves');
            $totalCostosLlaves = $llavesPorTecnico->sum('total_valor');
            Log::info('Total costos llaves:', ['valor' => $totalCostosLlaves]);
            
            Log::info('Calculando ganancia');
            $ganancia = ($totales['totalVentas'] ?? 0) 
                      - ($totales['totalCostos'] ?? 0) 
                      - ($totalCostosLlaves ?? 0) 
                      - ($totales['totalGastos'] ?? 0);
            Log::info('Ganancia calculada:', ['valor' => $ganancia]);

            Log::info('Consultando retiro del dueño');
            $retiroDueño = Nempleado::whereHas('empleado', function($query) {
                $query->where('cargo', 5); 
            })
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_pago', [$startDate, $endDate])
                     ->orWhereBetween('fecha_desde', [$startDate, $endDate])
                     ->orWhereBetween('fecha_hasta', [$startDate, $endDate]);
            })
            ->sum('total_pagado');
            
            Log::info('Retiro del dueño encontrado:', [
                'valor' => $retiroDueño,
                'fecha_inicio' => $startDate->format('Y-m-d'),
                'fecha_fin' => $endDate->format('Y-m-d')
            ]);
            
            Log::info('Calculando ganancia final');
            $gananciaFinal = $ganancia - $retiroDueño;
            Log::info('Ganancia final calculada:', [
                'ganancia' => $ganancia,
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal
            ]);

            Log::info('Generando PDF');
            Log::info('Consultando ventas detalladas por técnico');
            $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startDate, $endDate);
            Log::info('Ventas detalladas encontradas:', [
                'count' => count($ventasDetalladasPorTecnico),
                'first' => $ventasDetalladasPorTecnico->first() ?? null
            ]);

            Log::info('Consultando ventas por trabajo');
            $ventasPorTrabajo = $this->getVentasPorTrabajo($startDate, $endDate, $metodosPago);
            Log::info('Ventas por trabajo obtenidas:', [
                'contado_count' => count($ventasPorTrabajo['contado']),
                'credito_count' => count($ventasPorTrabajo['credito'])
            ]);

            Log::info('Consultando resumen de trabajos');
            $resumenTrabajos = $this->getResumenTrabajos($startDate, $endDate);
            Log::info('Resumen de trabajos obtenido:', [
                'count' => count($resumenTrabajos),
                'first' => $resumenTrabajos->first() ?? null
            ]);

            Log::info('Consultando ventas por lugar de venta');
            $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startDate, $endDate);
            Log::info('Ventas por lugar de venta obtenidas:', [
                'count' => count($ventasPorLugarVenta),
                'first' => $ventasPorLugarVenta->first() ?? null
            ]);

            Log::info('Consultando ventas por cliente');
            $ventasPorCliente = $this->getVentasPorCliente($startDate, $endDate);
            Log::info('Ventas por cliente obtenidas:', [
                'count' => count($ventasPorCliente),
                'first' => $ventasPorCliente->first() ?? null
            ]);

            Log::info('Procesando ventas por trabajo');
            $ventasPorTrabajo['contado'] = collect($ventasPorTrabajo['contado'])->map(function($trabajoData) use ($metodosPago) {
                $metodos = collect($trabajoData['metodos'])->map(function($metodoData, $metodoId) use ($metodosPago) {
                    $metodoNombre = $metodosPago->where('id', $metodoId)->first()?->name ?? 'Desconocido';
                    return [
                        'nombre' => $metodoNombre,
                        'total' => $metodoData['total'],
                        'count' => $metodoData['count']
                    ];
                });
                return [
                    'total' => $trabajoData['total'],
                    'metodos' => $metodos
                ];
            });

            $ventasPorTrabajo['credito'] = collect($ventasPorTrabajo['credito'])->map(function($trabajoData) use ($metodosPago) {
                $metodos = collect($trabajoData['metodos'])->map(function($metodoData, $metodoId) use ($metodosPago) {
                    $metodoNombre = $metodosPago->where('id', $metodoId)->first()?->name ?? 'Desconocido';
                    return [
                        'nombre' => $metodoNombre,
                        'total' => $metodoData['total'],
                        'count' => $metodoData['count']
                    ];
                });
                return [
                    'total' => $trabajoData['total'],
                    'metodos' => $metodos
                ];
            });

            Log::info('Ventas por trabajo procesadas:', [
                'contado_first' => $ventasPorTrabajo['contado']->first(),
                'credito_first' => $ventasPorTrabajo['credito']->first()
            ]);

            Log::info('Calculando total de descargas');
            $totalDescargas = $descargasManuales->where('tipo', 'ajuste2')->sum('cantidad');
            Log::info('Total de descargas:', ['valor' => $totalDescargas]);
            
            Log::info('Generando vista PDF');
            $data = [
                'ventasPorCliente' => $ventasPorCliente,
                'resumenTrabajos' => $resumenTrabajos,
                'ventasPorLugarVenta' => $ventasPorLugarVenta,
                'startDate' => $startDate->format('d/m/Y'),
                'endDate' => $endDate->format('d/m/Y'),
                'reporteVentas' => $reporteVentas,
                'reporteCostosGastos' => $reporteCostosGastos,
                'ingresosRecibidos' => $ingresosRecibidos,
                'llavesPorTecnico' => $llavesPorTecnico,
                'totales' => $totales,
                'totalCostosLlaves' => $totalCostosLlaves,
                'ganancia' => $ganancia,
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal,
                'metodosPago' => $metodosPago, 
                'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
                'tiposDePago' => TiposDePago::all(), 
                'almacenesDisponibles' => Almacene::all(),
                'cargasDescargas' => $descargasManuales,
                'totalDescargas' => $totalDescargas,
                'ventasPorTrabajo' => $ventasPorTrabajo
            ];
            Log::info('Datos para vista PDF:', [
                'count' => count($data),
                'keys' => array_keys($data)
            ]);

            Log::info('Cargando vista PDF');
            $pdf = PDF::loadView('estadisticas.cierre-semanal-pdf', $data);
            Log::info('Vista PDF cargada');

            Log::info('Generando nombre de archivo');
            $fileName = 'cierre-semanal-' . $startDate->format('Y') . '-' . $startDate->format('W') . '.pdf';
            Log::info('Nombre de archivo generado:', ['filename' => $fileName]);

            Log::info('Iniciando descarga de PDF');
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error en exportPdf:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'context' => [
                    'startDate' => $startDate ? $startDate->format('Y-m-d') : null,
                    'endDate' => $endDate ? $endDate->format('Y-m-d') : null
                ]
            ]);
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $startDate = Carbon::parse($request->input('year', now()->year) . 'W' . $request->input('week', now()->weekOfYear))->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        $metodosPago = TiposDePago::all();

        $reporteVentas = $this->getVentasPorTecnico($startDate, $endDate);
        $reporteCostosGastos = $this->getCostosGastosPorTecnico($startDate, $endDate, $metodosPago);
        $ingresosRecibidos = $this->getIngresosRecibidos($startDate, $endDate, $metodosPago);
        $llavesPorTecnico = $this->getLlavesPorTecnico($startDate, $endDate);
        $descargasManuales = $this->getCargasDescargas($startDate, $endDate);
        $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startDate, $endDate);

        $descargasManualesFormato = [
            'tecnico' => 'Manual',
            'llaves' => collect($descargasManuales)->groupBy('producto')->map(function($grupo, $producto) {
                $primerRegistro = $grupo->first();
                $idProducto = $primerRegistro['id_producto'] ?? null;
                
                $llave = [
                    'nombre' => Producto::where('id_producto', $primerRegistro['id_producto'])->value('item') ?? 'Producto no encontrado',
                    'id_producto' => $idProducto,
                    'almacenes' => collect($grupo)->groupBy('id_almacen')->map(function($almacenGrupo) use ($idProducto) {
                        $diferencias = $almacenGrupo->map(function($item) {
                            return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                        });
                        
                        $cantidad = $diferencias->sum();
                        $precio = Producto::where('id_producto', $almacenGrupo->first()['id_producto'])->value('precio') ?? 0;
                        return [
                            'cantidad' => $cantidad,
                            'total' => $cantidad * $precio,
                            'id_producto' => $idProducto
                        ];
                    }),
                    'total_cantidad' => $grupo->map(function($item) {
                        return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                    })->sum(),
                    'total_valor' => $grupo->map(function($item) {
                        $diferencia = abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                        $precio = Producto::where('id_producto', $item['id_producto'])->value('precio') ?? 0;
                        return $diferencia * $precio;
                    })->sum(),
                    'id_producto' => $idProducto
                ];
                return $llave;
            }),
            'total_llaves' => $descargasManuales->map(function($item) {
                return $item['cantidad'];
            })->sum(),
            'total_valor' => $descargasManuales->map(function($item) {
                $diferencia = abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                return $diferencia * ($item['precio'] ?? 0);
            })->sum()
        ];

        $llavesPorTecnico = collect($llavesPorTecnico);
        $llavesPorTecnico->push($descargasManualesFormato);

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

        $retiroDueño = $ganancia * 0.10;
        $gananciaFinal = $ganancia - $retiroDueño;

        
        $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startDate, $endDate);
        
        
        $ventasPorTrabajo = $this->getVentasPorTrabajo($startDate, $endDate, $metodosPago);
        
        
        $ventasPorCliente = $this->getVentasPorCliente($startDate, $endDate);
        

        $resumenTrabajos = $this->getResumenTrabajos($startDate, $endDate);

    
        $data = [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'reporteVentas' => $reporteVentas,
            'reporteCostosGastos' => $reporteCostosGastos,
            'ingresosRecibidos' => $ingresosRecibidos,
            'llavesPorTecnico' => $llavesPorTecnico,
            'totales' => $totales,
            'totalCostosLlaves' => $totalCostosLlaves,
            'ganancia' => $ganancia,
            'retiroDueño' => $retiroDueño,
            'gananciaFinal' => $gananciaFinal,
            'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
            'ventasPorLugarVenta' => $ventasPorLugarVenta,
            'ventasPorTrabajo' => $ventasPorTrabajo,
            'resumenTrabajos' => $resumenTrabajos,
            'ventasPorCliente' => $ventasPorCliente
        ];

        return Excel::download(new CierreSemanalExport($data), 'cierre-semanal-' . $startDate->format('Y') . '-' . $startDate->format('W') . '.xlsx');
    }

    private function getLlavesPorTecnico($startDate, $endDate)
    {
        return Empleado::with([
            'ventas' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            }
        ])
        ->whereHas('ventas', function($query) use ($startDate, $endDate) {
            $query->whereBetween('fecha_h', [$startDate, $endDate]);
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
                                $llaveNombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                                $almacenId = $producto['almacen'];
                                $cantidad = $producto['cantidad'];
                                $precio = $producto['precio'] ?? 0;

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
                $query->where('cargo', '!=', 5) 
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereHas('costos', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('f_costos', [$startDate, $endDate]);
                    })
                    ->orWhereHas('gastos', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('f_gastos', [$startDate, $endDate]);
                    })
                    ->orWhereHas('pagosEmpleados', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('fecha_pago', [$startDate, $endDate]);
                    });
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
                        'cliente' => $venta->cliente ? $venta->cliente->nombre : $venta->id_cliente,
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
            ->with('cliente')
            ->get()
            ->groupBy('id_cliente')
            ->map(function ($ventas, $idCliente) {
                $cliente = $ventas->first()->cliente;
                return [
                    'id_cliente' => $idCliente,
                    'cliente' => $cliente ? $cliente->nombre : $idCliente,
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
                    'descripcion' => 'Pago a empleado (Nómina)',
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
        $costosLlaves = $llavesData->sum('total_valor');

        return [
            'totalVentasContado' => collect($reporteVentas)->sum('ventas_contado'),
            'totalVentasCredito' => collect($reporteVentas)->sum('ventas_credito'),
            'totalVentas' => collect($reporteVentas)->sum('total_ventas'),
            'totalCostos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['costos'])->sum('total')) + $costosLlaves,
            'totalGastos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['gastos'])->sum('total')),
            'totalIngresosRecibidos' => $ingresosRecibidos->sum('total'),
            'totalGeneralLlaves' => $llavesData->sum('total_llaves'),
            'totalGeneralValorLlaves' => $costosLlaves
        ];
    }

    private function getCargasDescargas($startDate, $endDate)
    {
        return AjusteInventario::with(['producto', 'almacene', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('tipo_ajuste', 'ajuste2')
            ->get()
            ->map(function($ajuste) {
                return [
                    'usuario' => $ajuste->user->name,
                    'producto' => $ajuste->producto->nombre,
                    'id_producto' => $ajuste->producto->id_producto,
                    'almacen' => $ajuste->almacene->nombre,
                    'id_almacen' => $ajuste->almacene->id_almacen,
                    'tipo' => $ajuste->tipo_ajuste,
                    'cantidad' => abs($ajuste->diferencia), 
                    'cantidad_anterior' => $ajuste->cantidad_anterior,
                    'cantidad_nueva' => $ajuste->cantidad_nueva,
                    'motivo' => $ajuste->descripcion,
                    'fecha' => $ajuste->created_at->format('d/m/Y H:i'),
                    'es_carga' => false,
                    'precio' => $ajuste->producto->precio ?? 0
                ];
            });
    }

    private function getVentasAlContado($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->where('metodo_pce', 'contado')
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

    private function getVentasCredito($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->where('metodo_pce', 'credito')
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