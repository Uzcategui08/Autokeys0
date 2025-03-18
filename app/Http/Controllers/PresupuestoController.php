<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
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
    public function store(PresupuestoRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $idAlmacen = $request->input('almacen');
    
        $items = [];
        foreach ($request->input('items') as $item) {
            if (!empty($item['producto'])) {
                $items[] = [
                    'producto' => $item['producto'],
                    'cantidad' => $item['cantidad'],
                    'almacen' => $idAlmacen,
                ];
            }
        }
    
        $validatedData['items'] = json_encode($items);
    
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
    
        foreach ($items as &$item) {
            $producto = Producto::find($item['producto']);
            if ($producto) {
            $item['nombre_producto'] = $producto->item;
            $item['precio_producto'] = $producto->precio;
            } else {
            $item['nombre_producto'] = 'Producto no encontrado';
            $item['precio_producto'] = 0;
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

        $presupuesto = Presupuesto::find($id);

        $items = json_decode($presupuesto->items, true);
    
        foreach ($items as &$item) {
            $producto = Producto::find($item['producto']);
            if ($producto) {
                $item['nombre_producto'] = $producto->item; 
            } else {
                $item['nombre_producto'] = 'Producto no encontrado'; 
            }
        }

        $presupuesto->items = $items;

        $clientes = Cliente::all();
        $inventario = Inventario::with('producto')->get();
        $almacenes = Almacene::all();
    
        return view('presupuesto.edit', compact('presupuesto', 'clientes', 'inventario', 'almacenes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PresupuestoRequest $request, Presupuesto $presupuesto): RedirectResponse
    {
        $presupuesto->update($request->validated());

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
                ];
            });

        return response()->json($productos);
    }

    public function generarPdf($id)
    {
        $presupuesto = Presupuesto::find($id);

        $items = json_decode($presupuesto->items, true);

        foreach ($items as &$item) {
            $producto = Producto::find($item['producto']);
            if ($producto) {
            $item['nombre_producto'] = $producto->item;
            $item['precio_producto'] = $producto->precio;
            } else {
            $item['nombre_producto'] = 'Producto no encontrado';
            $item['precio_producto'] = 0;
            }
        }

        $presupuesto->items = $items;

        $pdf = Pdf::loadView('presupuesto.pdf', compact('presupuesto'));

        return $pdf->stream('presupuesto_' . $presupuesto->id . '.pdf');
    }
}
