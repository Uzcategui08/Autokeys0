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
    public function store(RegistroVRequest $request): RedirectResponse
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

        RegistroV::create($validatedData);


        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV creado exitosamente.');
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

        $registroV->update(Arr::except($validatedData, ['items']));

        if ($request->has('items')) {
            $registroV->items = json_encode($items);
            $registroV->save();
        }

        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV actualizado exitosamente');
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
