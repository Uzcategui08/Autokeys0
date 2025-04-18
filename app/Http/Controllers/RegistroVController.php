<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use App\Models\RegistroV;
use App\Models\Cliente;
use App\Models\Producto;
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
        $registroVs = RegistroV::with('cliente')->paginate(15);

        return view('registro-v.index', compact('registroVs'))
            ->with('i', ($request->input('page', 1) - 1) * $registroVs->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $clientes = Cliente::all();
        $inventario = Inventario::with('producto')->get();
        $almacenes = Almacene::all();
        $registroV = new RegistroV();

        return view('registro-v.create', compact('registroV', 'clientes', 'inventario', 'almacenes'));
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
    public function store(Request $request): RedirectResponse
    {
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
                                        'precio' => $producto['precio'],
                                        'almacen' => $producto['almacen'],
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
    
            RegistroV::create($validatedData);
    
    

            $pagosData = [];
            if ($request->has('pagos')) {
                $pagosInput = $request->input('pagos');
                if (is_string($pagosInput)) {
                    $pagosData = json_decode(trim($pagosInput, '"\' '), true) ?? [];
                } elseif (is_array($pagosInput)) {
                    $pagosData = $pagosInput;
                }
            }

            $gasto = new Gasto([
                'f_gastos' => $validated['f_gastos'],
                'id_tecnico' => $validated['id_tecnico'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'estatus' => $validated['estatus'],
                'pagos' => $pagosData
            ]);

            if (!$gasto->save()) {
                throw new \Exception("No se pudo guardar el registro en la base de datos");
            }

            return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV creado exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al guardar el gasto: ' . $e->getMessage()]);
        }
    }
    // Método para restar cantidad del inventario
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
     * Display the specified resource.
     */
    public function show($id): View
    {
        $registroV = RegistroV::find($id);

        $items = json_decode($registroV->items, true);

        if (is_array($items)) {
            foreach ($items as &$itemGroup) {
                if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                    foreach ($itemGroup['productos'] as &$producto) {
                        if (isset($producto['producto'])) {
                            $productoDetalle = Producto::find($producto['producto']);
                            if ($productoDetalle) {
                                $producto['nombre_producto'] = $productoDetalle->item;
                                $producto['precio_producto'] = $productoDetalle->precio;
                            } else {
                                $producto['nombre_producto'] = 'Producto no encontrado';
                                $producto['precio_producto'] = 0;
                            }
                        } else {
                            $producto['nombre_producto'] = 'Producto no especificado';
                            $producto['precio_producto'] = 0;
                        }
                    }
                }
            }
        }

        $registroV->items = $items;

        return view('registro-v.show', compact('registroV'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $registroV = RegistroV::findOrFail($id);
        $almacenes = Almacene::all();
        $clientes = Cliente::all();
    
        $items = json_decode($registroV->items, true) ?? [];
    
        foreach ($items as &$trabajo) {
            if (isset($trabajo['productos'])) {
                foreach ($trabajo['productos'] as &$producto) {
                    $producto['producto'] = $producto['producto'] ?? null;
                    $producto['cantidad'] = $producto['cantidad'] ?? 1;
                    $producto['precio'] = $producto['precio'] ?? null;
                    $producto['almacen'] = $producto['almacen'] ?? null;
    
                    if ($producto['producto']) {
                        $productoModel = Producto::find($producto['producto']);
                        $producto['nombre_producto'] = $productoModel ? $productoModel->item : 'Producto no encontrado';
    

                    } else {
                        $producto['nombre_producto'] = 'Producto no especificado';
                        $producto['stock_actual'] = 'No disponible';
                    }
                }
            } else {
                $trabajo['productos'] = [];
            }
        }
    
        $registroV->items = $items;
    
        return view('registro-v.edit', compact('registroV', 'almacenes', 'clientes'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(RegistroVRequest $request, RegistroV $registroV): RedirectResponse
    {
        $validatedData = $request->validated();
    
        $items = $request->input('items');
    
        // Obtener los datos antiguos
        $itemsAntiguos = json_decode($registroV->items, true) ?? [];
    
        // Ajustar el inventario según sea necesario
        $this->ajustarInventario($itemsAntiguos, $items);
    
        $registroV->update(Arr::except($validatedData, ['items']));
    
        if ($request->has('items')) {
            $registroV->items = json_encode($items);
            $registroV->save();
        }
    
        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV actualizado exitosamente');
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
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        RegistroV::find($id)->delete();

        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV eliminado exitosamente');
    }

    /**
     * Generar PDF del registro
     */
    public function generarPdf($id)
    {
        $registroV = RegistroV::find($id);

        $items = json_decode($registroV->items, true);

        if (is_array($items)) {
            foreach ($items as &$itemGroup) {
                if (isset($itemGroup['productos']) && is_array($itemGroup['productos'])) {
                    foreach ($itemGroup['productos'] as &$producto) {
                        if (isset($producto['producto'])) {
                            $productoDetalle = Producto::find($producto['producto']);
                            if ($productoDetalle) {
                                $producto['nombre_producto'] = $productoDetalle->item;
                                $producto['precio_producto'] = $productoDetalle->precio;
                            } else {
                                $producto['nombre_producto'] = 'Producto no encontrado';
                                $producto['precio_producto'] = 0;
                            }
                        } else {
                            $producto['nombre_producto'] = 'Producto no especificado';
                            $producto['precio_producto'] = 0;
                        }
                    }
                }
            }
        }

        $registroV->items = $items;

        $pdf = Pdf::loadView('registro-v.pdf', compact('registroV'));

        return $pdf->stream('registro_v_' . $registroV->id . '.pdf');
    }
}
