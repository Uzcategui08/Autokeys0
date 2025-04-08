<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use App\Models\Presupuesto;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PresupuestoRequest;
use App\Models\Almacene;
use App\Models\Inventario;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PresupuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $presupuestos = Presupuesto::with('cliente')->paginate();

        return view('presupuesto.index', compact('presupuestos'))
            ->with('i', ($request->input('page', 1) - 1) * $presupuestos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $clientes = Cliente::all();

        $inventario = Inventario::with('producto')->get();

        $almacenes = Almacene::all();

        $presupuesto = new Presupuesto();

        return view('presupuesto.create', compact('presupuesto', 'clientes', 'inventario', 'almacenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(presupuestoRequest $request): RedirectResponse
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
                                    'precio' => $producto['precio'],
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
    
        Presupuesto::create($validatedData);
    
        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $presupuesto = Presupuesto::find($id);
    
        $items = json_decode($presupuesto->items, true);
    
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

        $presupuesto->items = $items;

        return view('presupuesto.show', compact('presupuesto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $presupuesto = Presupuesto::findOrFail($id);
        $almacenes = Almacene::all();
        $clientes = Cliente::all();

        $items = json_decode($presupuesto->items, true) ?? [];
        
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
        
        $presupuesto->items = $items;
        
        return view('presupuesto.edit', compact('presupuesto', 'almacenes', 'clientes'));
    }
    

    /**
     * Update the specified resource in storage.

    public function update(PresupuestoRequest $request, Presupuesto $presupuesto): RedirectResponse
    {

        $presupuesto->update($request->validated());
    
        if ($request->has('items')) {
            foreach ($request->input('items') as $index => $itemData) {
                $productoId = $itemData['producto'] ?? $presupuesto->items[$index]['producto'] ?? null;
                $almacenId = $itemData['almacen'] ?? $presupuesto->items[$index]['almacen'] ?? null;
                $cantidad = $itemData['cantidad'] ?? $presupuesto->items[$index]['cantidad'] ?? null;
    
            }
        }
    
        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto updated successfully');
    }
     */

    public function update(PresupuestoRequest $request, Presupuesto $presupuesto): RedirectResponse
    {
        $validatedData = $request->validated();

        $items = $request->input('items');

        $presupuesto->update(Arr::except($validatedData, ['items']));

        if ($request->has('items')) {
            $presupuesto->items = json_encode($items);
            $presupuesto->save();
        }

        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto updated successfully');
    }
    
    

    public function destroy($id): RedirectResponse
    {
        Presupuesto::find($id)->delete();

        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto deleted successfully');
    }

    public function obtenerProductos(Request $request)
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
                    'precio' => $item->producto ? $item->producto->precio : 0,
                ];
            });

        return response()->json($productos);
    }

    public function generarPdf($id)
    {
        $presupuesto = Presupuesto::find($id);

        $items = json_decode($presupuesto->items, true);


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

        $presupuesto->items = $items;

        $pdf = Pdf::loadView('presupuesto.pdf', compact('presupuesto'));

        return $pdf->stream('presupuesto_' . $presupuesto->id_presupuesto . '.pdf');
    }
}
