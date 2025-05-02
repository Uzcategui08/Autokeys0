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
use App\Models\TiposDePago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegistroVRequest;
use App\Models\Almacene;
use App\Models\Inventario;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RegistroVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = RegistroV::with('cliente')
        ->where('estatus', 'pagado');
    
    // Si el usuario es limited_user, filtrar solo sus registros
    if (auth()->user()->hasRole('limited_user')) {
        $query->where('id_empleado', auth()->id());
    }
    $registroVs = $query->paginate(20);

        return view('registro-v.index', compact('registroVs'))
            ->with('i', ($request->input('page', 1) - 1) * $registroVs->perPage());
    }

public function cxc(Request $request): View
{
    $query = RegistroV::with('cliente')
        ->where('estatus', '!=', 'pagado');
    
    // Si el usuario es limited_user, filtrar solo sus registros
    if (auth()->user()->hasRole('limited_user')) {
        $query->where('id_empleado', auth()->id());
    }
    
    $registroVs = $query->paginate(20);

    return view('registro-v.cxc', compact('registroVs'))
        ->with('i', ($request->input('page', 1) - 1) * $registroVs->perPage());
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

        return view('registro-v.create', compact('registroV', 'clientes', 'inventario', 'almacenes', 'empleados','tiposDePago'));
    }

    /**
     * Obtener productos por almacén (AJAX)
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
    
        try {
            $validatedData = $request->validated();
    
            $trabajos = [];
            if ($request->has('items')) {
                foreach ($request->input('items') as $item) {
                    if (!empty($item['trabajo'])) {
                        $productos = [];
                        if (isset($item['productos'])) {
                            foreach ($item['productos'] as $producto) {
                                if (!empty($producto['producto'])) {
                                    $this->restarInventario($producto['producto'], $producto['cantidad']);
                                    
                                    $productos[] = [
                                        'producto' => $producto['producto'],
                                        'cantidad' => $producto['cantidad'],
                                        'almacen' => $producto['almacen'],
                                        'precio' => $producto['precio'] ?? 0,
                                    ];
                                }
                            }
                        }
                        $trabajos[] = [
                            'trabajo' => $item['trabajo'],
                            'productos' => $productos,
                        ];
                    }
                }
            }
            $validatedData['items'] = json_encode($trabajos);
    
            $costosIds = [];
            if ($request->has('costos_extras')) {
                foreach ($request->input('costos_extras') as $costoData) {
                    if (!empty($costoData['descripcion'])) {
                        $pagoCosto = [
                            [
                                'monto' => (float)($costoData['monto'] ?? 0),
                                'metodo_pago' => $costoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $validatedData['fecha_h'] ?? now()->format('Y-m-d')
                            ]
                        ];
    
                        $costo = Costo::create([
                            'f_costos' => $validatedData['fecha_h'] ?? now()->format('Y-m-d'),
                            'id_tecnico' => $request->input('id_empleado'),
                            'descripcion' => $costoData['descripcion'],
                            'subcategoria' => 'costo_extra',
                            'valor' => (float)($costoData['monto'] ?? 0),
                            'estatus' => 'pagado',
                            'pagos' => $pagoCosto
                        ]);
                        
                        $costosIds[] = $costo->id_costos;
                    }
                }
            }
            $validatedData['costos'] = $costosIds;
    
            $gastosIds = [];
            if ($request->has('gastos')) {
                foreach ($request->input('gastos') as $gastoData) {
                    if (!empty($gastoData['descripcion'])) {
                        $pagoGasto = [
                            [
                                'monto' => (float)($gastoData['monto'] ?? 0),
                                'metodo_pago' => $gastoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $validatedData['fecha_h'] ?? now()->format('Y-m-d')
                            ]
                        ];
    
                        $gasto = Gasto::create([
                            'f_gastos' => $validatedData['fecha_h'] ?? now()->format('Y-m-d'),
                            'id_tecnico' => $request->input('id_empleado'),
                            'descripcion' => $gastoData['descripcion'],
                            'subcategoria' => 'gasto_extra',
                            'valor' => (float)($gastoData['monto'] ?? 0),
                            'estatus' => 'pagado',
                            'pagos' => $pagoGasto,
                        ]);
                        
                        $gastosIds[] = $gasto->id_gastos;
                    }
                }
            }
            $validatedData['gastos'] = $gastosIds;
    
            $pagosValidados = [];
            $totalPagado = 0;
            
            if ($request->has('pagos')) {
                try {
                    $pagosInput = $request->input('pagos');
                    $pagos = is_string($pagosInput) ? json_decode($pagosInput, true) : $pagosInput;
                    
                    foreach ($pagos as $pago) {
                        if (!isset($pago['monto']) || !is_numeric($pago['monto']) || $pago['monto'] <= 0) {
                            continue;
                        }
                        
                        $pagosValidados[] = [
                            'monto' => (float) $pago['monto'],
                            'metodo_pago' => $pago['metodo_pago'] ?? 'efectivo',
                            'fecha' => $pago['fecha'] ?? now()->format('Y-m-d'),
                        ];
                        
                        $totalPagado += (float) $pago['monto'];
                    }
                    
                    $validatedData['pagos'] = json_encode($pagosValidados);
    
                    $valorTotal = (float) ($validatedData['valor_v'] ?? 0);
                    
                    if ($totalPagado >= $valorTotal) {
                        $validatedData['estatus'] = 'pagado';
                    } elseif ($totalPagado > 0) {
                        $validatedData['estatus'] = 'parcialemente pagado';
                    } else {
                        $validatedData['estatus'] = 'pendiente';
                    }
                    
                } catch (\Exception $e) {
                    $validatedData['pagos'] = json_encode([]);
                }
            } else {
                $validatedData['pagos'] = json_encode([]);
            }
    
            $registroV = RegistroV::create($validatedData);
    
            $abono = Abono::create([
                'a_fecha' => $registroV->fecha_h,
                'id_empleado' => $request->input('id_empleado'),
                'concepto' => $registroV->trabajo,
                'valor' => $registroV->porcentaje_c,
            ]);
            
            $registroV->id_abono = $abono->id_abonos;
            $registroV->save();
    
            DB::commit();
    
            return Redirect::route('registro-vs.index')
                ->with('success', 'Registro creado satisfactoriamente.');
    
        } catch (\Exception $e) {
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

        $items = json_decode($registroV->items, true) ?? [];
        
        foreach ($items as &$itemGroup) {
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
            'pagos' => $pagos
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
            $items = json_decode($registroV->items, true) ?? [];
            
            foreach ($items as &$trabajo) {
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
                        'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                        'cobro' => $costo->estatus,
                        'fecha' => $costo->f_costos
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
                        'metodo_pago' => $pagosData[0]['metodo_pago'] ?? null,
                        'estatus' => $gasto->estatus,
                        'fecha' => $gasto->fecha
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
            'pagosRegistro' => $pagosRegistro
        ];
        
        return view('registro-v.edit', $viewData);
    }

    // Método para ajustar el inventario
    private function ajustarInventario($itemsAntiguos, $itemsNuevos)
    {
        // Recorrer los items nuevos
        foreach ($itemsNuevos as $trabajoNuevo) {
            if (isset($trabajoNuevo['productos'])) {
                foreach ($trabajoNuevo['productos'] as $productoNuevo) {
                    // Buscar el producto en los items antiguos
                    $productoAntiguo = $this->buscarProductoAntiguo($itemsAntiguos, $trabajoNuevo['trabajo'], $productoNuevo['producto']);

                    if ($productoAntiguo) {
                        // Ajustar el inventario según la diferencia
                        $diferencia = $productoAntiguo['cantidad'] - $productoNuevo['cantidad'];
                        if ($diferencia != 0) {
                            $this->actualizarInventario($productoNuevo['producto'], $diferencia, $productoNuevo['almacen']);
                        }
                    } else {
                        // Si no se encontró el producto antiguo, restar la cantidad nueva del inventario
                        $this->actualizarInventario($productoNuevo['producto'], -$productoNuevo['cantidad'], $productoNuevo['almacen']);
                    }
                }
            }
        }

        // Recorrer los items antiguos para verificar si se eliminaron productos
        foreach ($itemsAntiguos as $trabajoAntiguo) {
            if (isset($trabajoAntiguo['productos'])) {
                foreach ($trabajoAntiguo['productos'] as $productoAntiguo) {
                    // Buscar el producto en los items nuevos
                    $productoNuevo = $this->buscarProductoNuevo($itemsNuevos, $trabajoAntiguo['trabajo'], $productoAntiguo['producto']);

                    if (!$productoNuevo) {
                        // Si no se encontró el producto nuevo, sumar la cantidad antigua al inventario
                        $this->actualizarInventario($productoAntiguo['producto'], $productoAntiguo['cantidad'], $productoAntiguo['almacen']);
                    }
                }
            }
        }
    }
     
    // Método para buscar un producto antiguo
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
    
    // Método para buscar un producto nuevo
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
     
    // Método para actualizar el inventario
    private function actualizarInventario($productoId, $cantidad, $almacen)
    {
        $inventario = Inventario::where('id_producto', $productoId)
            ->first();
    
        if ($inventario) {
            $nuevaCantidad = $inventario->cantidad + $cantidad;
            if ($nuevaCantidad < 0) {
                // Manejar el caso cuando la cantidad resultante es negativa
                throw new \Exception("No hay suficiente stock para el producto $productoId");
            }
    
            $inventario->update(['cantidad' => $nuevaCantidad]);
        } else {
            // Manejar el caso cuando no se encuentra el producto en el inventario
            throw new \Exception("El producto $productoId no existe en el inventario");
        }
    }

    private function restarInventario($productoId, $cantidad)
    {
        $inventario = Inventario::where('id_producto', $productoId)
            ->first();

        if ($inventario) {
            $nuevaCantidad = $inventario->cantidad - $cantidad;
            if ($nuevaCantidad < 0) {
                // Manejar el caso cuando la cantidad a restar es mayor que la existente
                // Puedes lanzar una excepción o mostrar un mensaje
                throw new \Exception("No hay suficiente stock para el producto $productoId");
            }

            $inventario->update(['cantidad' => $nuevaCantidad]);
        } else {
            // Manejar el caso cuando no se encuentra el producto en el inventario
            // Puedes lanzar una excepción o mostrar un mensaje
            throw new \Exception("El producto $productoId no existe en el inventario");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegistroVRequest $request, RegistroV $registroV): RedirectResponse
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validated();
    
            $items = $request->input('items');
            $itemsAntiguos = json_decode($registroV->items, true) ?? [];
            $this->ajustarInventario($itemsAntiguos, $items);
    
            $trabajos = [];
            if ($request->has('items')) {
                foreach ($request->input('items') as $item) {
                    if (!empty($item['trabajo'])) {
                        $productos = [];
                        if (isset($item['productos'])) {
                            foreach ($item['productos'] as $producto) {
                                if (!empty($producto['producto'])) {
                                    $productos[] = [
                                        'producto' => $producto['producto'],
                                        'cantidad' => $producto['cantidad'],
                                        'almacen' => $producto['almacen'],
                                        'precio' => $producto['precio'] ?? 0,
                                        'nombre_producto' => $producto['nombre_producto'] ?? null
                                    ];
                                }
                            }
                        }
                        $trabajos[] = [
                            'trabajo' => $item['trabajo'],
                            'productos' => $productos,
                        ];
                    }
                }
            }
            $validatedData['items'] = json_encode($trabajos);
    
            $costosIds = [];
            if ($request->has('costos_extras')) {
                foreach ($request->input('costos_extras') as $costoData) {
                    if (!empty($costoData['descripcion'])) {
                        $pagoCosto = [
                            [
                                'monto' => (float)($costoData['monto'] ?? 0),
                                'metodo_pago' => $costoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $costoData['fecha'] ?? now()->format('Y-m-d'),
                                'comprobante' => $costoData['comprobante'] ?? null
                            ]
                        ];
    
                        if (!empty($costoData['id_costos'])) {
                            $costo = Costo::find($costoData['id_costos']);
                            if ($costo) {
                                $costo->update([
                                    'descripcion' => $costoData['descripcion'],
                                    'valor' => (float)($costoData['monto'] ?? 0),
                                    'estatus' => 'pagado',
                                    'pagos' => $pagoCosto,
                                    'id_tecnico' => $request->input('id_empleado')
                                ]);
                                $costosIds[] = $costo->id_costos;
                                continue;
                            }
                        }
    
                        $nuevoCosto = Costo::create([
                            'f_costos' => $costoData['fecha'] ?? now()->format('Y-m-d'),
                            'id_tecnico' => $request->input('id_empleado'),
                            'descripcion' => $costoData['descripcion'],
                            'subcategoria' => 'costo_extra',
                            'valor' => (float)($costoData['monto'] ?? 0),
                            'estatus' => 'pagado',
                            'pagos' => $pagoCosto,
                            'id_registro_v' => $registroV->id
                        ]);
                        $costosIds[] = $nuevoCosto->id_costos;
                    }
                }
            }
            $validatedData['costos'] = json_encode($costosIds);
    
            $gastosIds = [];
            if ($request->has('gastos')) {
                foreach ($request->input('gastos') as $gastoData) {
                    if (!empty($gastoData['descripcion'])) {
                        $pagoGasto = [
                            [
                                'monto' => (float)($gastoData['monto'] ?? 0),
                                'metodo_pago' => $gastoData['metodo_pago'] ?? 'efectivo',
                                'fecha' => $gastoData['fecha'] ?? now()->format('Y-m-d'),
                                'comprobante' => $gastoData['comprobante'] ?? null
                            ]
                        ];
    
                        if (!empty($gastoData['id_gastos'])) {
                            $gasto = Gasto::find($gastoData['id_gastos']);
                            if ($gasto) {
                                $gasto->update([
                                    'descripcion' => $gastoData['descripcion'],
                                    'valor' => (float)($gastoData['monto'] ?? 0),
                                    'estatus' => $gastoData['estatus'] ?? 'pendiente',
                                    'pagos' => $pagoGasto,
                                    'id_empleado' => $request->input('id_empleado'),
                                    'id_registro_v' => $registroV->id
                                ]);
                                $gastosIds[] = $gasto->id_gastos;
                                continue;
                            }
                        }
    
                        $nuevoGasto = Gasto::create([
                            'fecha' => $gastoData['fecha'] ?? now()->format('Y-m-d'),
                            'id_empleado' => $request->input('id_empleado'),
                            'descripcion' => $gastoData['descripcion'],
                            'valor' => (float)($gastoData['monto'] ?? 0),
                            'estatus' => $gastoData['estatus'] ?? 'pendiente',
                            'pagos' => $pagoGasto,
                            'id_registro_v' => $registroV->id
                        ]);
                        $gastosIds[] = $nuevoGasto->id_gastos;
                    }
                }
            }
            $validatedData['gastos'] = json_encode($gastosIds);
    
            $pagosValidados = [];
            $totalPagado = 0;
            
            if ($request->has('pagos')) {
                try {
                    $pagosInput = $request->input('pagos');
                    $pagos = is_string($pagosInput) ? json_decode($pagosInput, true) : $pagosInput;
                    
                    foreach ($pagos as $pago) {
                        if (!isset($pago['monto']) || !is_numeric($pago['monto']) || $pago['monto'] <= 0) {
                            continue;
                        }
                        
                        $pagosValidados[] = [
                            'monto' => (float) $pago['monto'],
                            'metodo_pago' => $pago['metodo_pago'] ?? 'efectivo',
                            'fecha' => $pago['fecha'] ?? now()->format('Y-m-d'),
                        ];
                        
                        $totalPagado += (float) $pago['monto'];
                    }
                    
                    $validatedData['pagos'] = json_encode($pagosValidados);
    
                    $valorTotal = (float) ($validatedData['valor_v'] ?? $registroV->valor_v);
                    
                    if ($totalPagado >= $valorTotal) {
                        $validatedData['estatus'] = 'pagado';
                    } elseif ($totalPagado > 0) {
                        $validatedData['estatus'] = 'parcialemente pagado';
                    } else {
                        $validatedData['estatus'] = 'pendiente';
                    }
                    
                } catch (\Exception $e) {
                    $validatedData['pagos'] = json_encode([]);
                }
            } else {
                $validatedData['pagos'] = json_encode([]);
            }
    
            $registroV->update($validatedData);
    
            if ($registroV->id_abono) {
                Abono::where('id_abonos', $registroV->id_abono)->update([
                    'id_empleado' => $request->input('id_empleado'),
                    'valor' => $registroV->porcentaje_c,
                    'concepto' => $registroV->trabajo,
                    'a_fecha' => $registroV->fecha_h
                ]);
            } else {
                $abono = Abono::create([
                    'id_empleado' => $request->input('id_empleado'),
                    'valor' => $registroV->porcentaje_c,
                    'concepto' => $registroV->trabajo,
                    'a_fecha' => $registroV->fecha_h
                ]);
                $registroV->update(['id_abono' => $abono->id_abonos]);
            }
    
            DB::commit();
    
            return Redirect::route('registro-vs.index')
                ->with('success', 'Registro actualizado satisfactoriamente.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
 public function destroy($id): RedirectResponse
{
    DB::beginTransaction();

    try {
        $registroV = RegistroV::findOrFail($id);

        // No need to json_decode since Laravel already converts JSON columns to arrays
        $costosIds = $registroV->costos ?: [];
        $gastosIds = $registroV->gastos ?: [];

        if ($registroV->id_abono) {
            Abono::where('id_abonos', $registroV->id_abono)->delete();
        }

        if (!empty($costosIds)) {
            Costo::whereIn('id_costos', $costosIds)->delete();
        }

        if (!empty($gastosIds)) {
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

        $items = $registroV->items;
        if (is_string($items)) {
            $items = json_decode($items, true);
        }
        
        if (is_array($items)) {
            foreach ($items as &$itemGroup) {
                if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                    foreach ($itemGroup['productos'] as &$producto) {
                        if (isset($producto['producto'])) {
                            $productoDetalle = Producto::find($producto['producto']);
                            if ($productoDetalle) {
                                $producto['nombre_producto'] = $productoDetalle->item;
                                $producto['precio_producto'] = $productoDetalle->precio;
                                $producto['id_producto'] = $productoDetalle->id_producto;
                            } else {
                                $producto['nombre_producto'] = 'Producto no encontrado';
                                $producto['precio_producto'] = 0;
                                $producto['id_producto'] = 'N/A';
                            }
                        } else {
                            $producto['nombre_producto'] = 'Producto no especificado';
                            $producto['precio_producto'] = 0;
                            $producto['id_producto'] = 'N/A';
                        }
                    }
                }
            }
        } else {
            $items = [];
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
    
        $registroV->items = $items;
        $registroV->pagos = $pagos;
        $registroV->total_pagado = $totalPagado;
        $registroV->saldo_pendiente = $saldoPendiente;
    
        $pdf = Pdf::loadView('registro-v.pdf', compact('registroV'));
    
        return $pdf->stream('registro_v_' . $registroV->id . '.pdf');
    }

    public function generatePdf($id)
    {
        $registroV = RegistroV::findOrFail($id);

        $items = $registroV->items;
        if (is_string($items)) {
            $items = json_decode($items, true);
        }
        
        if (is_array($items)) {
            foreach ($items as &$itemGroup) {
                if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                    foreach ($itemGroup['productos'] as &$producto) {
                        if (isset($producto['producto'])) {
                            $productoDetalle = Producto::find($producto['producto']);
                            if ($productoDetalle) {
                                $producto['nombre_producto'] = $productoDetalle->item;
                                $producto['precio_producto'] = $productoDetalle->precio;
                                $producto['id_producto'] = $productoDetalle->id_producto;
                            } else {
                                $producto['nombre_producto'] = 'Producto no encontrado';
                                $producto['precio_producto'] = 0;
                                $producto['id_producto'] = 'N/A';
                            }
                        } else {
                            $producto['nombre_producto'] = 'Producto no especificado';
                            $producto['precio_producto'] = 0;
                            $producto['id_producto'] = 'N/A';
                        }
                    }
                }
            }
        } else {
            $items = [];
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
    
        $registroV->items = $items;
        $registroV->pagos = $pagos;
        $registroV->total_pagado = $totalPagado;
        $registroV->saldo_pendiente = $saldoPendiente;
    
        $pdf = Pdf::loadView('registro-v.invoice', compact('registroV'));
    
        return $pdf->stream('invoice' . $registroV->id . '.pdf');
    }
}
