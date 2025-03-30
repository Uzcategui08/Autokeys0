<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Inventario;
use App\Models\Almacene;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\OrdenRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {

        $ordens = Orden::with('cliente')->paginate();

        return view('orden.index', compact('ordens'))
            ->with('i', ($request->input('page', 1) - 1) * $ordens->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $inventario = Inventario::with('producto')->get();

        $almacenes = Almacene::all();

        $clientes = Cliente::all();

        $orden = new Orden();

        return view('orden.create', compact('orden', 'inventario', 'almacenes', 'clientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrdenRequest $request): RedirectResponse
    {

        $validatedData = $request->validated();

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
    
        Orden::create($validatedData);
    
        return Redirect::route('ordens.index')
            ->with('success', 'Orden creada exitosamente.');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $orden = Orden::find($id);
    
        $items = json_decode($orden->items, true);
    
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

        $orden->items = $items;

        return view('orden.show', compact('orden'));
    }
    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $orden = Orden::findOrFail($id);
        $almacenes = Almacene::all();
        $clientes = Cliente::all();

        $items = json_decode($orden->items, true) ?? [];
        
        foreach ($items as &$trabajo) {
            if (isset($trabajo['productos'])) {
                foreach ($trabajo['productos'] as &$producto) {
                    $producto['producto'] = $producto['producto'] ?? null;
                    $producto['cantidad'] = $producto['cantidad'] ?? 1;
                    $producto['almacen'] = $producto['almacen'] ?? null;
                    
                    if ($producto['producto']) {
                        $productoModel = Producto::find($producto['producto']);
                        $producto['nombre_producto'] = $productoModel ? $productoModel->item : 'Producto no encontrado';
                    } else {
                        $producto['nombre_producto'] = 'Producto no especificado';
                    }
                }
            } else {
                $trabajo['productos'] = [];
            }
        }
        
        $orden->items = $items;
        
        return view('orden.edit', compact('orden', 'almacenes', 'clientes'));
    }
    
    

    /**
     * Update the specified resource in storage.

    public function update(OrdenRequest $request, Orden $orden): RedirectResponse
    {

        $orden->update($request->validated());
    
        if ($request->has('items')) {
            foreach ($request->input('items') as $index => $itemData) {
                $productoId = $itemData['producto'] ?? $orden->items[$index]['producto'] ?? null;
                $almacenId = $itemData['almacen'] ?? $orden->items[$index]['almacen'] ?? null;
                $cantidad = $itemData['cantidad'] ?? $orden->items[$index]['cantidad'] ?? null;
    
            }
        }

        return Redirect::route('ordens.index')
            ->with('success', 'Orden updated successfully');
    }
    */

    public function update(OrdenRequest $request, Orden $orden): RedirectResponse
    {
        $validatedData = $request->validated();

        $items = $request->input('items');

        $orden->update(Arr::except($validatedData, ['items']));

        if ($request->has('items')) {
            $orden->items = json_encode($items);
            $orden->save();
        }

        return Redirect::route('ordens.index')
            ->with('success', 'Orden updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Orden::find($id)->delete();

        return Redirect::route('ordens.index')
            ->with('success', 'Orden deleted successfully');
    }

    public function obtenerProductos(Request $request)
    {
        $idAlmacen = $request->input('id_almacen');

        $productos = Inventario::where('id_almacen', $idAlmacen)
                               ->with('producto')
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

    public function generarPdf($id)
    {
        $orden = Orden::find($id);

        $items = json_decode($orden->items, true);

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

        $orden->items = $items;
    
        $pdf = Pdf::loadView('orden.pdf', compact('orden'));
    
        return $pdf->stream('orden_' . $orden->id_orden . '.pdf');
    }
}
