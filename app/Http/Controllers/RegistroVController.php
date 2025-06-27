<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\RegistroV;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Empleado;
use App\Models\Abono;
use App\Models\Costo;
use App\Models\Gasto;
use App\Models\Categoria;
use App\Models\TiposDePago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegistroVRequest;
use App\Models\Almacene;
use App\Models\Inventario;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;

class RegistroVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = RegistroV::with(['cliente', 'empleado'])
            ->where('estatus', 'pagado')
            ->select([
                'registroV.*',
                DB::raw("jsonb_path_query_array(items::jsonb, '$.trabajo[*]')::text as tipo_trabajo"),
                DB::raw("jsonb_path_query_array(pagos::jsonb, '$.metodo_pago[*]')::text as metodo_pago"),
                'titular_c',
                DB::raw("jsonb_path_query_array(items::jsonb, '$.productos[*].nombre_producto')::text as tipo_llave"),
                DB::raw("jsonb_path_query_array(items::jsonb, '$.productos[*].cantidad')::text as cantidad_utilizada")
            ]);

        if (auth()->user()->hasRole('limited')) {
            $query->where('id_empleado', auth()->id());
        }

        $registroVs = $query->get(); // Paginación
        $tiposDePago = TiposDePago::all()->keyBy('id');
        $productos = Producto::all()->keyBy('item');

        return view('registro-v.index', compact('registroVs', 'tiposDePago', 'productos'));
    }

    public function cxc(Request $request): View
    {
        $query = RegistroV::with(['cliente', 'empleado'])
            ->where('estatus', '!=', 'pagado')
            ->select([
                'registroV.*',
                DB::raw("jsonb_path_query_array(items::jsonb, '$.trabajo[*]')::text as tipo_trabajo"),
                DB::raw("jsonb_path_query_array(pagos::jsonb, '$.metodo_pago[*]')::text as metodo_pago"),
                'titular_c',
                DB::raw("jsonb_path_query_array(items::jsonb, '$.productos[*].nombre_producto')::text as tipo_llave"),
                DB::raw("jsonb_path_query_array(items::jsonb, '$.productos[*].cantidad')::text as cantidad_utilizada")
            ]);

        if (auth()->user()->hasRole('limited')) {
            $query->where('id_empleado', auth()->id());
        }

        $registroVs = $query->get();

        return view('registro-v.cxc', compact('registroVs'));
    }


    public function toggleCargado(RegistroV $registroV)
    {
        try {
            $registroV->cargado = $registroV->cargado ? 0 : 1; // fuerza 0 o 1
            $registroV->save();
            $registroV->refresh();
            return response()->json([
                'cargado' => (int)$registroV->cargado // fuerza entero
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'cargado' => isset($registroV->cargado) ? (int)$registroV->cargado : null
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tiposDePago = TiposDePago::all();
        $clientes = Cliente::all();
        $inventario = Inventario::with('producto')->get();
        $almacenes = Almacene::all();
        $registroV = new RegistroV();
        $empleados = Empleado::where('cargo', '1')->get();
        $trabajos = Trabajo::all();
        $categorias = Categoria::all();

        return view('registro-v.create', compact('registroV', 'clientes', 'inventario', 'almacenes', 'empleados', 'tiposDePago', 'trabajos', 'categorias'));
    }


    public function obtenerTodosLosTrabajos()
    {
        try {
            $trabajos = Trabajo::orderBy('nombre', 'asc')
                ->get(['id_trabajo', 'nombre', 'traducciones']);

            return response()->json($trabajos);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los trabajos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos por almacÃ©n (AJAX)
     */
    public function obtenerProductosV(Request $request)
    {
        $idAlmacen = $request->input('id_almacen');

        $productos = Inventario::with('producto')
            ->where('id_almacen', $idAlmacen)
            ->get()
            ->map(function ($item) {
                return [
                    'id_producto' => $item->id_producto,
                    'item' => $item->producto->item,
                    'cantidad' => $item->cantidad,
                ];
            });

        return response()->json($productos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegistroVRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        Log::info('Iniciando transacción para crear registro de venta');
        Log::debug('Datos recibidos en request', ['request' => $request->all()]);

        try {
            Log::info('Validando datos del request');
            $validatedData = $request->validated();
            Log::debug('Datos validados', ['validatedData' => $validatedData]);

            $trabajos = [];
            Log::info('Procesando items de trabajos');
            if ($request->has('items')) {
                foreach ($request->input('items') as $index => $item) {
                    Log::debug("Procesando item {$index}", ['item' => $item]);
                    $trabajoId = $item['trabajo_id'] ?? null;
                    $descripcionTrabajo = $item['trabajo_nombre'] ?? $item['trabajo'] ?? null;
                    $precioTrabajo = $item['precio_trabajo'] ?? 0;

                    if ($trabajoId && empty($descripcionTrabajo)) {
                        Log::debug("Buscando trabajo con ID {$trabajoId}");
                        $trabajo = Trabajo::find($trabajoId);
                        $descripcionTrabajo = $trabajo ? $trabajo->nombre : 'Trabajo no especificado';
                        $precioTrabajo = $precioTrabajo ?: ($trabajo ? $trabajo->precio : 0);
                    }

                    if (!empty($descripcionTrabajo)) {
                        $productos = [];
                        if (isset($item['productos'])) {
                            Log::debug("Procesando productos para item {$index}");
                            foreach ($item['productos'] as $productoIndex => $producto) {
                                if (!empty($producto['producto'])) {
                                    Log::debug("Restando inventario para producto {$productoIndex}", ['producto' => $producto]);
                                    $this->restarInventario($producto['producto'], $producto['cantidad'], $producto['almacen'], $validatedData['fecha_h'] ?? now()->format('Y-m-d'));
                                    $productos[] = [
                                        'producto' => $producto['producto'],
                                        'cantidad' => $producto['cantidad'],
                                        'almacen' => $producto['almacen'],
                                        'precio' => $producto['precio'] ?? 0,
                                        'nombre_producto' => $producto['nombre_producto'] ?? null,
                                    ];
                                }
                            }
                        }

                        $trabajos[] = [
                            'trabajo_id' => $trabajoId,
                            'trabajo' => $descripcionTrabajo,
                            'precio_trabajo' => (float)$precioTrabajo,
                            'descripcion' => $item['descripcion'] ?? null,
                            'productos' => $productos,
                        ];
                    }
                }
            }
            $validatedData['items'] = json_encode($trabajos);
            Log::debug('Items procesados', ['items' => $trabajos]);

            $costosIds = [];
            Log::info('Procesando costos extras');
            if ($request->has('costos_extras')) {
                foreach ($request->input('costos_extras') as $costoIndex => $costoData) {
                    if (!empty($costoData['descripcion'])) {
                        Log::debug("Procesando costo extra {$costoIndex}", ['costo' => $costoData]);
                        $pagoCosto = [
                            [
                                'monto' => (float)($costoData['monto'] ?? 0),
                                'metodo_pago' => $costoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $validatedData['fecha_h'] ?? now()->format('Y-m-d'),
                                'cobrador_id' => $costoData['metodo_pago'] ? $request->input('id_empleado') : null
                            ]
                        ];

                        $costo = Costo::create([
                            'f_costos' => $costoData['f_costos'],
                            'id_tecnico' => $request->input('id_empleado'),
                            'descripcion' => $costoData['descripcion'],
                            'subcategoria' => $costoData['subcategoria'],
                            'valor' => (float)($costoData['monto'] ?? 0),
                            'estatus' => 'pagado',
                            'pagos' => $pagoCosto
                        ]);

                        $costosIds[] = $costo->id_costos;
                    }
                }
            }
            $validatedData['costos'] = $costosIds;
            Log::debug('Costos extras procesados', ['costos' => $costosIds]);

            $gastosIds = [];
            Log::info('Procesando gastos');
            if ($request->has('gastos')) {
                foreach ($request->input('gastos') as $gastoIndex => $gastoData) {
                    if (!empty($gastoData['descripcion'])) {
                        Log::debug("Procesando gasto {$gastoIndex}", ['gasto' => $gastoData]);
                        $pagoGasto = [
                            [
                                'monto' => (float)($gastoData['monto'] ?? 0),
                                'metodo_pago' => $gastoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $validatedData['fecha_h'] ?? now()->format('Y-m-d'),
                                'cobrador_id' => $gastoData['metodo_pago'] ? $request->input('id_empleado') : null
                            ]
                        ];

                        $gasto = Gasto::create([
                            'f_gastos' => $gastoData['f_gastos'],
                            'id_tecnico' => $request->input('id_empleado'),
                            'descripcion' => $gastoData['descripcion'],
                            'subcategoria' => $gastoData['subcategoria'],
                            'valor' => (float)($gastoData['monto'] ?? 0),
                            'estatus' => 'pagado',
                            'pagos' => $pagoGasto,
                        ]);

                        $gastosIds[] = $gasto->id_gastos;
                    }
                }
            }
            $validatedData['gastos'] = $gastosIds;
            Log::debug('Gastos procesados', ['gastos' => $gastosIds]);

            $pagosValidados = [];
            $totalPagado = 0;
            Log::info('Procesando pagos');

            if ($request->has('pagos')) {
                try {
                    $pagosInput = $request->input('pagos');
                    Log::debug('Pagos recibidos', ['pagos_input' => $pagosInput]);
                    $pagos = is_string($pagosInput) ? json_decode($pagosInput, true) : $pagosInput;

                    foreach ($pagos as $pagoIndex => $pago) {
                        if (!isset($pago['monto']) || !is_numeric($pago['monto']) || $pago['monto'] <= 0) {
                            Log::warning("Pago {$pagoIndex} inválido", ['pago' => $pago]);
                            continue;
                        }

                        $pagosValidados[] = [
                            'monto' => (float) $pago['monto'],
                            'metodo_pago' => $pago['metodo_pago'] ?? 'efectivo',
                            'fecha' => $pago['fecha'] ?? now()->format('Y-m-d'),
                            'cobrador_id' => $pago['cobrador_id'] ?? $request->input('id_empleado')
                        ];

                        $totalPagado += (float) $pago['monto'];
                    }

                    $validatedData['pagos'] = json_encode($pagosValidados);
                    Log::debug('Pagos validados', ['pagos' => $pagosValidados]);

                    $valorTotal = (float) ($validatedData['valor_v'] ?? 0);
                    Log::debug('Calculando estado del pago', ['total_pagado' => $totalPagado, 'valor_total' => $valorTotal]);

                    if ($totalPagado >= $valorTotal) {
                        $validatedData['estatus'] = 'pagado';
                    } elseif ($totalPagado > 0) {
                        $validatedData['estatus'] = 'parcialemente pagado';
                    } else {
                        $validatedData['estatus'] = 'pendiente';
                    }
                } catch (\Exception $e) {
                    Log::error('Error al procesar pagos', ['error' => $e->getMessage()]);
                    $validatedData['pagos'] = json_encode([]);
                }
            } else {
                Log::info('No se recibieron pagos');
                $validatedData['pagos'] = json_encode([]);
            }

            Log::info('Creando registro de venta');
            $registroV = RegistroV::create($validatedData);
            Log::debug('Registro de venta creado', ['registro_id' => $registroV->id, 'registro' => $registroV]);
            if (!$registroV || !$registroV->id) {
                Log::error('No se pudo crear el registro de venta.');
                throw new \Exception('No se pudo crear el registro de venta.');
            }

            Log::info('Creando abono asociado');
            $abono = Abono::create([
                'a_fecha' => $registroV->fecha_h,
                'id_empleado' => $request->input('id_empleado'),
                'concepto' => 'Abono por venta #' . $registroV->id,
                'valor' => $registroV->porcentaje_c,
            ]);

            $registroV->id_abono = $abono->id_abonos;
            $registroV->save();
            Log::debug('Abono creado y asociado', ['abono_id' => $abono->id_abonos]);

            DB::commit();
            Log::info('Transacción completada exitosamente');

            return Redirect::route('registro-vs.index')
                ->with('success', 'Registro creado satisfactoriamente.');
        } catch (\Exception $e) {
            Log::error('Error en transacción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear el registro: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $registroV = RegistroV::findOrFail($id);
        $almacenes = Almacene::all();
        $tiposDePago = TiposDePago::all();
        $trabajos = Trabajo::all();
        $categorias = Categoria::all();
        $empleados = Empleado::all();

        $items = json_decode($registroV->items, true) ?? [];

        foreach ($items as &$itemGroup) {
            if (isset($itemGroup['trabajo_id']) && $itemGroup['trabajo_id']) {
                $trabajo = Trabajo::find($itemGroup['trabajo_id']);
                if ($trabajo) {
                    if (!isset($itemGroup['precio_trabajo'])) {
                        $itemGroup['precio_trabajo'] = $trabajo->precio;
                    }
                    if (!isset($itemGroup['descripcion'])) {
                        $itemGroup['descripcion'] = $trabajo->descripcion;
                    }
                }
            }

            if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                foreach ($itemGroup['productos'] as &$producto) {
                    if (isset($producto['producto'])) {
                        $productoDetalle = Producto::find($producto['producto']);
                        if ($productoDetalle) {
                            $producto['nombre_producto'] = $productoDetalle->item;
                            $producto['codigo_producto'] = $productoDetalle->id_producto;
                            $producto['precio_producto'] = $productoDetalle->precio_venta ?? $productoDetalle->precio;
                        }
                    }
                }
            }
        }

        $costosExtras = [];
        $costosIds = [];

        if ($registroV->costos) {
            $costosIds = is_string($registroV->costos) ? json_decode($registroV->costos, true) ?? [] : $registroV->costos;
        }

        if (!empty($costosIds)) {
            $costos = Costo::whereIn('id_costos', $costosIds)->get();

            foreach ($costos as $costo) {
                $pagosData = $costo->pagos;
                if (is_string($pagosData)) {
                    $pagosData = json_decode($pagosData, true) ?? [];
                }

                $costosExtras[] = [
                    'id_costos' => $costo->id_costos,
                    'descripcion' => $costo->descripcion,
                    'monto' => $costo->valor,
                    'subcategoria' => $costo->subcategoria,
                    'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                    'cobro' => $costo->estatus,
                    'fecha' => $costo->f_costos
                ];
            }
        }

        $gastos = [];
        $gastosIds = [];

        if ($registroV->gastos) {
            $gastosIds = is_string($registroV->gastos) ? json_decode($registroV->gastos, true) ?? [] : $registroV->gastos;
        }

        if (!empty($gastosIds)) {
            $gastosModels = Gasto::whereIn('id_gastos', $gastosIds)->get();

            foreach ($gastosModels as $gasto) {
                $pagosData = $gasto->pagos;
                if (is_string($pagosData)) {
                    $pagosData = json_decode($pagosData, true) ?? [];
                }

                $gastos[] = [
                    'id_gastos' => $gasto->id_gastos,
                    'descripcion' => $gasto->descripcion,
                    'monto' => $gasto->valor,
                    'subcategoria' => $gasto->subcategoria,
                    'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                    'estatus' => $gasto->estatus,
                    'fecha' => $gasto->fecha
                ];
            }
        }

        $pagos = [];

        if ($registroV->pagos) {
            $pagosData = $registroV->pagos;
            if (is_string($pagosData)) {
                $pagosData = json_decode($pagosData, true) ?? [];
            }

            if (is_array($pagosData)) {
                foreach ($pagosData as $pago) {
                    $pagos[] = [
                        'monto' => $pago['monto'] ?? 0,
                        'metodo_pago' => $pago['metodo_pago'] ?? 'Desconocido',
                        'fecha' => $pago['fecha'] ?? $registroV->fecha_h,
                        'cobrador_id' => $pago['cobrador_id'] ?? null,
                        'referencia' => $pago['referencia'] ?? null
                    ];
                }
            }
        }

        return view('registro-v.show', [
            'registroV' => $registroV,
            'almacenes' => $almacenes,
            'tiposDePago' => $tiposDePago,
            'items' => $items,
            'costosExtras' => $costosExtras,
            'gastos' => $gastos,
            'pagos' => $pagos,
            'trabajos' => $trabajos,
            'categorias' => $categorias,
            'empleados' => $empleados
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id): View
    {
        try {
            $tiposDePago = TiposDePago::all();
            $empleados = Empleado::where('cargo', '1')->get();
            $almacenes = Almacene::all();
            $clientes = Cliente::all();
            $trabajos = Trabajo::all();
            $categorias = Categoria::all();
        } catch (\Exception $e) {
            throw $e;
        }

        try {
            $registroV = RegistroV::findOrFail($id);

            $empleadoId = null;
            if ($registroV->tecnico) {
                $empleado = Empleado::where('nombre', $registroV->tecnico)->first();
                $empleadoId = $empleado ? $empleado->id_empleado : null;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        try {
            $registroV = RegistroV::findOrFail($id);

            $items = json_decode($registroV->items, true) ?? [];

            foreach ($items as $index => &$trabajo) {
                $trabajo['index'] = $index;
                if (!isset($trabajo['trabajo_id']) && isset($trabajo['trabajo'])) {
                    $trabajoModel = Trabajo::where('nombre', $trabajo['trabajo'])->first();
                    if ($trabajoModel) {
                        $trabajo['trabajo_id'] = $trabajoModel->id_trabajo;
                        $trabajo['trabajo_nombre'] = $trabajoModel->nombre;
                    }
                }

                $trabajo['trabajo_id'] = $trabajo['trabajo_id'] ?? null;
                $trabajo['trabajo'] = $trabajo['trabajo'] ?? ($trabajo['trabajo_nombre'] ?? 'Trabajo no especificado');
                $trabajo['trabajo_nombre'] = $trabajo['trabajo_nombre'] ?? $trabajo['trabajo'];
                $trabajo['precio_trabajo'] = $trabajo['precio_trabajo'] ?? 0;
                $trabajo['descripcion'] = $trabajo['descripcion'] ?? '';

                if ($trabajo['trabajo_id'] && $trabajo['precio_trabajo'] == 0) {
                    $trabajoModel = Trabajo::find($trabajo['trabajo_id']);
                    if ($trabajoModel) {
                        $trabajo['precio_trabajo'] = $trabajoModel->precio;
                    }
                }

                if (isset($trabajo['productos'])) {
                    foreach ($trabajo['productos'] as &$producto) {
                        $producto['producto'] = $producto['producto'] ?? null;
                        $producto['cantidad'] = $producto['cantidad'] ?? 1;
                        $producto['almacen'] = $producto['almacen'] ?? null;
                        $producto['precio'] = $producto['precio'] ?? 0;

                        if ($producto['producto']) {
                            $productoModel = Producto::find($producto['producto']);
                            $producto['nombre_producto'] = $productoModel ? $productoModel->item : 'Producto no encontrado';
                            $producto['codigo_producto'] = $productoModel ? $productoModel->id_producto : null;
                        }
                    }
                }
            }

            $registroV->items = $items;
        } catch (\Exception $e) {
            throw $e;
        }

        $costosExtras = [];
        try {
            $costosIds = [];
            if ($registroV->costos) {
                $costosIds = is_string($registroV->costos) ? json_decode($registroV->costos, true) ?? [] : $registroV->costos;
            }

            if (!empty($costosIds)) {
                $costos = Costo::whereIn('id_costos', $costosIds)->get();

                foreach ($costos as $costo) {
                    $pagosData = $costo->pagos;
                    if (is_string($pagosData)) {
                        $pagosData = json_decode($pagosData, true) ?? [];
                    }

                    $costosExtras[] = [
                        'id_costos' => $costo->id_costos,
                        'descripcion' => $costo->descripcion,
                        'monto' => $costo->valor,
                        'subcategoria' => $costo->subcategoria,
                        'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                        'cobro' => $costo->estatus,
                        'f_costos' => $costo->f_costos
                    ];
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $gastosData = [];
        try {
            $gastosIds = [];
            if ($registroV->gastos) {
                $gastosIds = is_string($registroV->gastos) ? json_decode($registroV->gastos, true) ?? [] : $registroV->gastos;
            }

            if (!empty($gastosIds)) {
                $gastos = Gasto::whereIn('id_gastos', $gastosIds)->get();

                foreach ($gastos as $gasto) {
                    $pagosData = $gasto->pagos;
                    if (is_string($pagosData)) {
                        $pagosData = json_decode($pagosData, true) ?? [];
                    }

                    $gastosData[] = [
                        'id_gastos' => $gasto->id_gastos,
                        'descripcion' => $gasto->descripcion,
                        'monto' => $gasto->valor,
                        'subcategoria' => $gasto->subcategoria,
                        'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                        'estatus' => $gasto->estatus,
                        'f_gastos' => $gasto->f_gastos
                    ];
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $pagosRegistro = [];
        try {
            if ($registroV->pagos) {
                $pagosData = $registroV->pagos;
                if (is_string($pagosData)) {
                    $pagosData = json_decode($pagosData, true) ?? [];
                }

                if (is_array($pagosData)) {
                    foreach ($pagosData as $pago) {
                        $pagosRegistro[] = [
                            'monto' => $pago['monto'] ?? 0,
                            'metodo_pago' => $pago['metodo_pago'] ?? 'Desconocido',
                            'fecha' => $pago['fecha'] ?? $registroV->fecha_h
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $viewData = [
            'registroV' => $registroV,
            'almacenes' => $almacenes,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'empleadoId' => $empleadoId,
            'tiposDePago' => $tiposDePago,
            'costosExtras' => $costosExtras,
            'gastosData' => $gastosData,
            'pagosRegistro' => $pagosRegistro,
            'trabajos' => $trabajos,
            'categorias' => $categorias
        ];

        return view('registro-v.edit', $viewData);
    }

    // MÃ©todo para ajustar el inventario
    private function ajustarInventario($itemsAntiguos, $itemsNuevos)
    {

        foreach ($itemsAntiguos as $trabajoAntiguo) {
            if (isset($trabajoAntiguo['productos'])) {
                foreach ($trabajoAntiguo['productos'] as $productoAntiguo) {
                    if (!empty($productoAntiguo['producto'])) {
                        $this->actualizarInventario(
                            $productoAntiguo['producto'],
                            $productoAntiguo['cantidad'],
                            $productoAntiguo['almacen'] ?? null
                        );
                    }
                }
            }
        }

        foreach ($itemsNuevos as $trabajoNuevo) {
            if (isset($trabajoNuevo['productos'])) {
                foreach ($trabajoNuevo['productos'] as $productoNuevo) {
                    if (!empty($productoNuevo['producto'])) {
                        $this->actualizarInventario(
                            $productoNuevo['producto'],
                            -$productoNuevo['cantidad'],
                            $productoNuevo['almacen'] ?? null
                        );
                    }
                }
            }
        }
    }

    // MÃ©todo para buscar un producto antiguo
    private function buscarProductoAntiguo($itemsAntiguos, $trabajo, $producto)
    {
        foreach ($itemsAntiguos as $trabajoAntiguo) {
            if ($trabajoAntiguo['trabajo'] == $trabajo) {
                foreach ($trabajoAntiguo['productos'] as $p) {
                    if ($p['producto'] == $producto) {
                        return [
                            'cantidad' => $p['cantidad'],
                            'almacen' => $p['almacen'],
                        ];
                    }
                }
            }
        }
        return null;
    }

    // MÃ©todo para buscar un producto nuevo
    private function buscarProductoNuevo($itemsNuevos, $trabajo, $producto)
    {
        foreach ($itemsNuevos as $trabajoNuevo) {
            if ($trabajoNuevo['trabajo'] == $trabajo) {
                foreach ($trabajoNuevo['productos'] as $p) {
                    if ($p['producto'] == $producto) {
                        return [
                            'cantidad' => $p['cantidad'],
                            'almacen' => $p['almacen'],
                        ];
                    }
                }
            }
        }
        return null;
    }

    public function verificarStock(Request $request)
    {
        Log::info('Inicio de verificaciÃ³n de stock', [
            'request_data' => $request->all(),
            'time' => now()
        ]);

        $productoId = $request->input('producto_id');
        $almacenId = $request->input('almacen_id');
        $cantidadRequerida = (int)$request->input('cantidad');
        $ventaId = $request->input('venta_id');

        Log::debug('Datos recibidos', [
            'producto_id' => $productoId,
            'almacen_id' => $almacenId,
            'cantidad_requerida' => $cantidadRequerida,
            'venta_id' => $ventaId
        ]);

        $inventario = Inventario::where('id_producto', $productoId)
            ->where('id_almacen', $almacenId)
            ->first();

        $stockDisponible = $inventario ? $inventario->cantidad : 0;

        $cantidadOriginal = 0;
        if ($ventaId) {
            $venta = RegistroV::find($ventaId);
            if ($venta) {
                $items = json_decode($venta->items, true) ?? [];
                foreach ($items as $item) {
                    if (isset($item['productos'])) {
                        foreach ($item['productos'] as $producto) {
                            if ($producto['producto'] == $productoId && $producto['almacen'] == $almacenId) {
                                $cantidadOriginal += $producto['cantidad'];
                            }
                        }
                    }
                }

                $stockDisponible += $cantidadOriginal;
                Log::debug('Ajuste por venta existente', [
                    'cantidad_original' => $cantidadOriginal,
                    'stock_ajustado' => $stockDisponible
                ]);
            }
        }

        $suficiente = $stockDisponible >= $cantidadRequerida;

        Log::info('Resultado de verificaciÃ³n de stock', [
            'stock_disponible' => $stockDisponible,
            'cantidad_requerida' => $cantidadRequerida,
            'suficiente' => $suficiente,
            'cantidad_original' => $cantidadOriginal
        ]);

        return response()->json([
            'suficiente' => $suficiente,
            'stock' => $stockDisponible,
            'producto_id' => $productoId,
            'almacen_id' => $almacenId,
            'cantidad_original' => $cantidadOriginal
        ]);
    }

    // Método para actualizar el inventario
    private function actualizarInventario($productoId, $cantidad, $almacenId = null)
    {
        $query = Inventario::where('id_producto', $productoId);

        if ($almacenId) {
            $query->where('id_almacen', $almacenId);
        }

        $inventario = $query->first();

        if ($inventario) {
            $nuevaCantidad = $inventario->cantidad + $cantidad;

            if ($nuevaCantidad < 0) {
                throw new \Exception("No hay suficiente stock para el producto $productoId");
            }

            $inventario->update(['cantidad' => $nuevaCantidad]);
        } else {
            throw new \Exception("El producto $productoId no existe en el inventario" . ($almacenId ? " para el almacén $almacenId" : ""));
        }
    }

    private function restarInventario($productoId, $cantidad, $almacenId = null, $fecha = null)
    {
        $query = Inventario::where('id_producto', $productoId);
        if ($almacenId) {
            $query->where('id_almacen', $almacenId);
        }
        $inventario = $query->first();

        if ($inventario) {
            $cantidadAnterior = $inventario->cantidad;
            $nuevaCantidad = $cantidadAnterior - $cantidad;
            if ($nuevaCantidad < 0) {
                throw new \Exception("No hay suficiente stock para el producto $productoId");
            }
            $inventario->update(['cantidad' => $nuevaCantidad]);

            // Registrar ajuste de inventario tipo "Llave de venta"
            \App\Models\AjusteInventario::create([
                'id_producto' => $productoId,
                'id_almacen' => $almacenId ?? $inventario->id_almacen,
                'tipo_ajuste' => 'resta',
                'cantidad_anterior' => $cantidadAnterior,
                'cantidad_nueva' => $nuevaCantidad,
                'descripcion' => $fecha,
                'user_id' => Auth::id(),
            ]);
        } else {
            throw new \Exception("El producto $productoId no existe en el inventario" . ($almacenId ? " para el almacén $almacenId" : ""));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegistroVRequest $request, RegistroV $registroV): RedirectResponse
    {
        DB::beginTransaction();

        Log::info('Iniciando actualización de registro V', ['registro_id' => $registroV->id]);

        try {
            Log::debug('Validando datos del request');
            $validatedData = $request->validated();
            Log::debug('Datos validados', ['data' => $validatedData]);

            // Procesamiento de items
            Log::debug('Procesando items');
            $items = $request->input('items');
            $itemsAntiguos = json_decode($registroV->items, true) ?? [];
            Log::debug('Items antiguos', ['items' => $itemsAntiguos]);
            Log::debug('Items nuevos', ['items' => $items]);

            try {
                $this->ajustarInventario($itemsAntiguos, $items);
                Log::info('Inventario ajustado correctamente');
            } catch (\Exception $e) {
                Log::error('Error al ajustar inventario', ['error' => $e->getMessage()]);
                throw $e;
            }

            $trabajos = [];
            if ($request->has('items')) {
                Log::debug('Procesando items detallados');
                foreach ($request->input('items') as $index => $item) {
                    try {
                        $trabajoId = $item['trabajo_id'] ?? null;
                        $descripcionTrabajo = $item['trabajo_nombre'] ?? $item['trabajo'] ?? null;
                        $precioTrabajo = $item['precio_trabajo'] ?? 0;
                        $descripcion = $item['descripcion'] ?? '';
                        Log::debug("Procesando item $index", ['item' => $item]);

                        if ($trabajoId && empty($descripcionTrabajo)) {
                            Log::debug("Buscando trabajo con ID $trabajoId");
                            $trabajo = Trabajo::find($trabajoId);
                            $descripcionTrabajo = $trabajo ? $trabajo->nombre : 'Trabajo no especificado';
                            $precioTrabajo = $precioTrabajo ?: ($trabajo ? $trabajo->precio : 0);
                            Log::debug("Trabajo encontrado", ['trabajo' => $trabajo]);
                        }

                        if (!empty($descripcionTrabajo)) {
                            $productos = [];
                            if (isset($item['productos'])) {
                                Log::debug("Procesando productos para item $index");
                                foreach ($item['productos'] as $pIndex => $producto) {
                                    if (!empty($producto['producto'])) {
                                        $productos[] = [
                                            'producto' => $producto['producto'],
                                            'cantidad' => $producto['cantidad'],
                                            'almacen' => $producto['almacen'],
                                            'precio' => $producto['precio'] ?? 0,
                                            'nombre_producto' => $producto['nombre_producto'] ?? null
                                        ];
                                        Log::debug("Producto $pIndex agregado", ['producto' => $producto]);
                                    }
                                }
                            }

                            $trabajos[] = [
                                'trabajo_id' => $trabajoId,
                                'trabajo' => $descripcionTrabajo,
                                'trabajo_nombre' => $descripcionTrabajo,
                                'precio_trabajo' => (float)$precioTrabajo,
                                'descripcion' => $descripcion,
                                'productos' => $productos,
                            ];
                            Log::debug("Trabajo agregado", ['trabajo' => end($trabajos)]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error procesando item $index", ['error' => $e->getMessage(), 'item' => $item]);
                        throw $e;
                    }
                }
            }
            $validatedData['items'] = json_encode($trabajos);
            Log::debug('Items procesados', ['trabajos' => $trabajos]);

            // Procesamiento de costos extras
            Log::debug('Procesando costos extras');
            $costosIds = [];
            if ($request->has('costos_extras')) {
                foreach ($request->input('costos_extras') as $cIndex => $costoData) {
                    try {
                        if (!empty($costoData['descripcion'])) {
                            $pagoCosto = [
                                [
                                    'monto' => (float)($costoData['monto'] ?? 0),
                                    'metodo_pago' => $costoData['metodo_pago'] ?? 'efectivo',
                                    'fecha' => $costoData['fecha'] ?? now()->format('Y-m-d'),
                                    'comprobante' => $costoData['comprobante'] ?? null
                                ]
                            ];
                            Log::debug("Procesando costo extra $cIndex", ['costo' => $costoData]);

                            if (!empty($costoData['id_costos'])) {
                                Log::debug("Actualizando costo existente", ['id_costo' => $costoData['id_costos']]);
                                $costo = Costo::find($costoData['id_costos']);
                                if ($costo) {
                                    $costo->update([
                                        'descripcion' => $costoData['descripcion'],
                                        'valor' => (float)($costoData['monto'] ?? 0),
                                        'subcategoria' => $costoData['subcategoria'],
                                        'estatus' => 'pagado',
                                        'pagos' => $pagoCosto,
                                        'id_tecnico' => $request->input('id_empleado'),
                                        'f_costos' => $costoData['f_costos'],
                                    ]);
                                    $costosIds[] = $costo->id_costos;
                                    Log::debug("Costo actualizado", ['id_costo' => $costo->id_costos]);
                                    continue;
                                }
                            }

                            Log::debug("Creando nuevo costo");
                            $nuevoCosto = Costo::create([
                                'f_costos' => $costoData['f_costos'],
                                'id_tecnico' => $request->input('id_empleado'),
                                'descripcion' => $costoData['descripcion'],
                                'subcategoria' => $costoData['subcategoria'],
                                'valor' => (float)($costoData['monto'] ?? 0),
                                'estatus' => 'pagado',
                                'pagos' => $pagoCosto,
                                'id_registro_v' => $registroV->id
                            ]);
                            $costosIds[] = $nuevoCosto->id_costos;
                            Log::debug("Nuevo costo creado", ['id_costo' => $nuevoCosto->id_costos]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error procesando costo extra $cIndex", ['error' => $e->getMessage(), 'costo' => $costoData]);
                        throw $e;
                    }
                }
            }
            $validatedData['costos'] = json_encode($costosIds);
            Log::debug('Costos procesados', ['costos_ids' => $costosIds]);

            // Procesamiento de gastos
            Log::debug('Procesando gastos');
            $gastosIds = [];
            if ($request->has('gastos')) {
                foreach ($request->input('gastos') as $gIndex => $gastoData) {
                    try {
                        if (!empty($gastoData['descripcion'])) {
                            $pagoGasto = [
                                [
                                    'monto' => (float)($gastoData['monto'] ?? 0),
                                    'metodo_pago' => $gastoData['metodo_pago'] ?? 'efectivo',
                                    'fecha' => $gastoData['fecha'] ?? now()->format('Y-m-d'),
                                    'comprobante' => $gastoData['comprobante'] ?? null
                                ]
                            ];
                            Log::debug("Procesando gasto $gIndex", ['gasto' => $gastoData]);

                            if (!empty($gastoData['id_gastos'])) {
                                Log::debug("Actualizando gasto existente", ['id_gasto' => $gastoData['id_gastos']]);
                                $gasto = Gasto::find($gastoData['id_gastos']);
                                if ($gasto) {
                                    $gasto->update([
                                        'descripcion' => $gastoData['descripcion'],
                                        'valor' => (float)($gastoData['monto'] ?? 0),
                                        'estatus' => 'pagado',
                                        'subcategoria' => $gastoData['subcategoria'],
                                        'pagos' => $pagoGasto,
                                        'f_gastos' => $gastoData['f_gastos'],
                                        'id_empleado' => $request->input('id_empleado'),
                                        'id_registro_v' => $registroV->id
                                    ]);
                                    $gastosIds[] = $gasto->id_gastos;
                                    Log::debug("Gasto actualizado", ['id_gasto' => $gasto->id_gastos]);
                                    continue;
                                }
                            }

                            Log::debug("Creando nuevo gasto");
                            $nuevoGasto = Gasto::create([
                                'f_gastos' => $gastoData['f_gastos'],
                                'id_tecnico' => $request->input('id_empleado'),
                                'descripcion' => $gastoData['descripcion'],
                                'subcategoria' => $gastoData['subcategoria'],
                                'valor' => (float)($gastoData['monto'] ?? 0),
                                'estatus' => 'pagado',
                                'pagos' => $pagoGasto,
                                'id_registro_v' => $registroV->id
                            ]);
                            $gastosIds[] = $nuevoGasto->id_gastos;
                            Log::debug("Nuevo gasto creado", ['id_gasto' => $nuevoGasto->id_gastos]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error procesando gasto $gIndex", ['error' => $e->getMessage(), 'gasto' => $gastoData]);
                        throw $e;
                    }
                }
            }
            $validatedData['gastos'] = json_encode($gastosIds);
            Log::debug('Gastos procesados', ['gastos_ids' => $gastosIds]);

            // Procesamiento de pagos
            Log::debug('Procesando pagos');
            $pagosValidados = [];
            $totalPagado = 0;

            if ($request->has('pagos')) {
                try {
                    $pagosInput = $request->input('pagos');
                    $pagos = is_string($pagosInput) ? json_decode($pagosInput, true) : $pagosInput;
                    Log::debug('Datos de pagos recibidos', ['pagos' => $pagos]);

                    foreach ($pagos as $pIndex => $pago) {
                        try {
                            if (!isset($pago['monto']) || !is_numeric($pago['monto']) || $pago['monto'] <= 0) {
                                Log::warning("Pago $pIndex ignorado por monto inválido", ['pago' => $pago]);
                                continue;
                            }

                            $pagosValidados[] = [
                                'monto' => (float) $pago['monto'],
                                'metodo_pago' => $pago['metodo_pago'] ?? 'efectivo',
                                'fecha' => $pago['fecha'] ?? now()->format('Y-m-d'),
                                'cobrador_id' => $pago['cobrador_id'] ?? null
                            ];

                            $totalPagado += (float) $pago['monto'];
                            Log::debug("Pago $pIndex procesado", ['pago' => end($pagosValidados), 'total_acumulado' => $totalPagado]);
                        } catch (\Exception $e) {
                            Log::error("Error procesando pago $pIndex", ['error' => $e->getMessage(), 'pago' => $pago]);
                            throw $e;
                        }
                    }

                    $validatedData['pagos'] = json_encode($pagosValidados);
                    Log::debug('Pagos validados', ['pagos' => $pagosValidados]);

                    $valorTotal = (float) ($validatedData['valor_v'] ?? $registroV->valor_v);
                    Log::debug('Calculando estatus de pago', ['total_pagado' => $totalPagado, 'valor_total' => $valorTotal]);

                    if ($totalPagado >= $valorTotal) {
                        $validatedData['estatus'] = 'pagado';
                    } elseif ($totalPagado > 0) {
                        $validatedData['estatus'] = 'parcialemente pagado';
                    } else {
                        $validatedData['estatus'] = 'pendiente';
                    }
                    Log::debug('Estatus determinado', ['estatus' => $validatedData['estatus']]);
                } catch (\Exception $e) {
                    Log::error('Error al procesar pagos', ['error' => $e->getMessage()]);
                    $validatedData['pagos'] = json_encode([]);
                }
            } else {
                Log::debug('No se recibieron datos de pagos');
                $validatedData['pagos'] = json_encode([]);
            }

            // Actualización del registro principal
            Log::debug('Actualizando registro principal', ['data' => $validatedData]);
            $registroV->update($validatedData);
            Log::info('Registro principal actualizado', ['registro_id' => $registroV->id]);

            // Procesamiento de abono
            Log::debug('Procesando abono');
            if ($registroV->id_abono) {
                Log::debug('Actualizando abono existente', ['id_abono' => $registroV->id_abono]);
                Abono::where('id_abonos', $registroV->id_abono)->update([
                    'id_empleado' => $request->input('id_empleado'),
                    'valor' => $registroV->porcentaje_c,
                    'concepto' => 'Abono por venta #' . $registroV->id,
                    'a_fecha' => $registroV->fecha_h
                ]);
                Log::info('Abono actualizado', ['id_abono' => $registroV->id_abono]);
            } else {
                Log::debug('Creando nuevo abono');
                $abono = Abono::create([
                    'id_empleado' => $request->input('id_empleado'),
                    'valor' => $registroV->porcentaje_c,
                    'concepto' => 'Abono por venta #' . $registroV->id,
                    'a_fecha' => $registroV->fecha_h
                ]);
                $registroV->update(['id_abono' => $abono->id_abonos]);
                Log::info('Nuevo abono creado', ['id_abono' => $abono->id_abonos]);
            }

            DB::commit();
            Log::info('Transacción completada con éxito', ['registro_id' => $registroV->id]);

            return Redirect::route('registro-vs.index')
                ->with('success', 'Registro actualizado satisfactoriamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en la transacción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'registro_id' => $registroV->id
            ]);
            return back()->withInput()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $registroV = RegistroV::findOrFail($id);

            $items = json_decode($registroV->items, true) ?? [];
            foreach ($items as $item) {
                if (isset($item['productos'])) {
                    foreach ($item['productos'] as $producto) {
                        if (!empty($producto['producto'])) {
                            $this->actualizarInventario($producto['producto'], $producto['cantidad'], $producto['almacen'] ?? null);
                        }
                    }
                }
            }

            $costosIds = is_string($registroV->costos) ? json_decode($registroV->costos, true) ?? [] : ($registroV->costos ?: []);
            $gastosIds = is_string($registroV->gastos) ? json_decode($registroV->gastos, true) ?? [] : ($registroV->gastos ?: []);

            if ($registroV->id_abono) {
                Abono::where('id_abonos', $registroV->id_abono)->delete();
            }

            if (is_array($costosIds) && count($costosIds) > 0) {
                Costo::whereIn('id_costos', $costosIds)->delete();
            }

            if (is_array($gastosIds) && count($gastosIds) > 0) {
                Gasto::whereIn('id_gastos', $gastosIds)->delete();
            }

            $registroV->delete();

            DB::commit();

            return Redirect::route('registro-vs.index')
                ->with('success', 'Registro eliminado satisfactoriamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF del registro
     */
    public function generarPdf($id)
    {
        $registroV = RegistroV::findOrFail($id);
        $tiposDePago = TiposDePago::all();

        $items = $registroV->items;
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items)) {
            $items = [];
        }

        foreach ($items as &$itemGroup) {
            if (isset($itemGroup['trabajo_id'])) {
                $trabajo = Trabajo::find($itemGroup['trabajo_id']);
                if ($trabajo) {
                    $itemGroup['trabajo'] = $trabajo->getNombreEnIdioma('es');
                }
            }

            if (!isset($itemGroup['productos']) || !is_array($itemGroup['productos'])) {
                $itemGroup['productos'] = [];
            }

            foreach ($itemGroup['productos'] as &$producto) {
                if (isset($producto['producto'])) {
                    $productoDetalle = Producto::find($producto['producto']);
                    $producto['nombre_completo'] = $productoDetalle ? $productoDetalle->nombre : 'Producto no encontrado';
                    $producto['precio_unitario'] = $productoDetalle ? $productoDetalle->precio : 0;
                }
            }
        }

        $pagos = $registroV->pagos;
        if (is_string($pagos)) {
            $pagos = json_decode($pagos, true);
        }

        if (!is_array($pagos)) {
            $pagos = [];
        }

        $totalPagado = collect($pagos)->sum('monto');
        $saldoPendiente = max($registroV->valor_v - $totalPagado, 0);

        $data = [
            'registroV' => $registroV,
            'items' => $items,
            'pagos' => $pagos,
            'total_pagado' => $totalPagado,
            'saldo_pendiente' => $saldoPendiente,
            'tiposDePago' => $tiposDePago
        ];

        $pdf = Pdf::loadView('registro-v.pdf', $data);

        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);

        return $pdf->stream('recibo_' . $registroV->id . '.pdf');
    }

    public function generatePdf($id)
    {
        $registroV = RegistroV::findOrFail($id);
        $tiposDePago = TiposDePago::all();

        $items = $registroV->items;
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items)) {
            $items = [];
        }

        foreach ($items as &$itemGroup) {

            if (isset($itemGroup['trabajo_id'])) {
                $trabajo = Trabajo::find($itemGroup['trabajo_id']);
                if ($trabajo) {
                    $nombreEnIngles = $trabajo->getNombreEnIdioma('en');
                    \Log::info('Nombre del trabajo en inglés: ' . $nombreEnIngles);
                    $itemGroup['trabajo'] = $nombreEnIngles;
                }
            }


            if (!isset($itemGroup['productos']) || !is_array($itemGroup['productos'])) {
                $itemGroup['productos'] = [];
            }

            foreach ($itemGroup['productos'] as &$producto) {
                if (isset($producto['producto'])) {
                    $productoDetalle = Producto::find($producto['producto']);
                    $producto['nombre_completo'] = $productoDetalle ? $productoDetalle->nombre : 'Producto no encontrado';
                    $producto['precio_unitario'] = $productoDetalle ? $productoDetalle->precio : 0;
                }
            }
        }

        $pagos = $registroV->pagos;
        if (is_string($pagos)) {
            $pagos = json_decode($pagos, true);
        }

        if (!is_array($pagos)) {
            $pagos = [];
        }

        $totalPagado = collect($pagos)->sum('monto');
        $saldoPendiente = max($registroV->valor_v - $totalPagado, 0);

        $data = [
            'registroV' => $registroV,
            'items' => $items,
            'pagos' => $pagos,
            'total_pagado' => $totalPagado,
            'saldo_pendiente' => $saldoPendiente,
            'tiposDePago' => $tiposDePago
        ];

        $pdf = Pdf::loadView('registro-v.invoice', $data);

        $pdf->setPaper('letter', 'portrait');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);

        return $pdf->stream('invoice' . $registroV->id . '.pdf');
    }

    public function reporteCxc(Request $request)
    {

        $fechaDesde = $request->fecha_desde ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $fechaHasta = $request->fecha_hasta ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

        $clientes = Cliente::orderBy('nombre')->get();

        if (!$request->has('fecha_desde')) {
            return view('registro-v.reporte', compact('clientes', 'fechaDesde', 'fechaHasta'));
        }

        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $query = RegistroV::whereBetween('fecha_h', [$fechaDesde, $fechaHasta])
            ->where('tipo_venta', 'credito');

        if ($request->cliente_id) {
            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $query->where('cliente', $cliente->nombre);
            }
        }

        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }

        $resumenClientes = $query->get()->groupBy('cliente')->map(function ($ventas, $clienteNombre) {
            $totalPagado = $ventas->sum(function ($venta) {
                if (is_array($venta->pagos)) {
                    return collect($venta->pagos)->sum('monto');
                }
                return 0;
            });

            return (object) [
                'cliente' => $clienteNombre,
                'telefono' => $ventas->first()->telefono,
                'total_ventas' => $ventas->count(),
                'total_ventas_monto' => $ventas->sum('valor_v'),
                'total_pagado' => $totalPagado,
                'saldo_pendiente' => $ventas->sum('valor_v') - $totalPagado
            ];
        })->values();

        return view('registro-v.reporte', compact(
            'clientes',
            'fechaDesde',
            'fechaHasta',
            'resumenClientes'
        ));
    }

    public function detalleCliente(Request $request)
    {
        $request->validate([
            'cliente' => 'required|string',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date'
        ]);

        $ventas = RegistroV::where('cliente', $request->cliente)
            ->whereBetween('fecha_h', [$request->fecha_desde, $request->fecha_hasta])
            ->where('tipo_venta', 'credito')
            ->get()
            ->map(function ($venta) {
                $totalPagado = 0;

                if ($venta->pagos && is_array($venta->pagos)) {
                    $totalPagado = collect($venta->pagos)->sum('monto');
                }
                $trabajos = [];
                $items = is_string($venta->items) ? json_decode($venta->items, true) : $venta->items;
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $trabajos[] = [
                            'nombre' => $item['trabajo_nombre'] ?? $item['trabajo'] ?? '',
                            'descripcion' => $item['descripcion'] ?? '',
                        ];
                    }
                }
                return [
                    'id' => $venta->id,
                    'fecha' => \Carbon\Carbon::parse($venta->fecha_h)->format('d/m/Y'),
                    'total_venta' => (float) $venta->valor_v,
                    'total_pagado' => (float) $totalPagado,
                    'saldo_pendiente' => (float) ($venta->valor_v - $totalPagado),
                    'estatus' => $venta->estatus
                ];
            });

        return response()->json($ventas);
    }

    public function cxcPdf(Request $request)
    {
        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;
        $clienteId = $request->cliente_id;
        $estatus = $request->estatus;
        $language = $request->language ?? 'es';

        $query = RegistroV::whereBetween('fecha_h', [$fechaDesde, $fechaHasta])
            ->with(['cliente']);

        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
            if ($cliente) {
                $query->where('cliente', $cliente->nombre);
            }
        }

        if ($estatus) {
            $query->where('estatus', $estatus);
        }

        $ventas = $query->get();

        $data = $ventas->groupBy('cliente')->map(function ($ventasGrupo, $clienteNombre) use ($language) {
            $totalPagado = $ventasGrupo->sum(function ($venta) {
                return is_array($venta->pagos) ? collect($venta->pagos)->sum('monto') : 0;
            });

            return (object) [
                'cliente' => $clienteNombre,
                'telefono' => $ventasGrupo->first()->telefono,
                'total_ventas' => $ventasGrupo->count(),
                'total_ventas_monto' => $ventasGrupo->sum('valor_v'),
                'total_pagado' => $totalPagado,
                'saldo_pendiente' => $ventasGrupo->sum('valor_v') - $totalPagado,
                'ventas' => $ventasGrupo->map(function ($venta) use ($language) {
                    $pagos = is_array($venta->pagos) ? $venta->pagos : [];
                    $totalPagadoVenta = collect($pagos)->sum('monto');

                    $items = [];
                    if ($venta->items) {
                        $itemsData = is_string($venta->items) ? json_decode($venta->items) : $venta->items;

                        if (is_array($itemsData) || is_object($itemsData)) {
                            foreach ($itemsData as $item) {
                                $item = (object) $item;
                                $items[] = (object) [
                                    'trabajo' => (function() use ($item, $language) {
                                        $trabajoNombre = $item->trabajo ?? $item->trabajo_nombre ?? null;
                                        if (isset($item->trabajo_id)) {
                                            $trabajoModel = \App\Models\Trabajo::find($item->trabajo_id);
                                            if ($trabajoModel && method_exists($trabajoModel, 'getNombreEnIdioma')) {
                                                return $trabajoModel->getNombreEnIdioma($language);
                                            }
                                        }
                                        return $trabajoNombre ?? ($language === 'en' ? 'Unspecified work' : 'Trabajo no especificado');
                                    })(),
                                    'precio_trabajo' => $item->precio_trabajo ?? ($language === 'en' ? 'Unspecified price' : 'Precio no especificado'),
                                    'descripcion' => $item->descripcion ?? null,
                                    'productos' => isset($item->productos) ? array_map(function ($p) use ($language) {
                                        $p = (object) $p;
                                        return (object) [
                                            'nombre_producto' => $p->nombre_producto ?? ($language === 'en' ? 'Unspecified product' : 'Producto no especificado'),
                                            'cantidad' => $p->cantidad ?? 1
                                        ];
                                    }, (array) $item->productos) : []
                                ];
                            }
                        }
                    }

                    return (object) [
                        'id' => $venta->id,
                        'id_empleado' => $venta->id_empleado,
                        'fecha_h' => $venta->fecha_h,
                        'valor_v' => $venta->valor_v,
                        'total_pagado' => $totalPagadoVenta,
                        'pagos' => $pagos,
                        'items' => $items
                    ];
                })
            ];
        })->values();

        $viewName = $language === 'en' ? 'registro-v.invoicecxc' : 'registro-v.reportecxc';

        $pdf = PDF::loadView($viewName, [
            'data' => $data,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'totalSaldo' => $data->sum('saldo_pendiente'),
            'tiposDePago' => TiposDePago::all(),
            'language' => $language
        ]);

        $filename = $language === 'en' ? 'accounts-receivable-report.pdf' : 'reporte-cxc-detallado.pdf';

        return $pdf->stream($filename);
    }
}
