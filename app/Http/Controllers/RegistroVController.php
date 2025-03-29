<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegistroVRequest;
use App\Models\Almacene;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RegistroVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $registroVs = RegistroV::paginate();

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
        RegistroV::create($validatedData);

        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $registroV = RegistroV::find($id);

        $items = json_decode($registroV->items, true);

        foreach ($items as &$item) {
            $producto = registroV::find($item['producto']);
            if ($producto) {
                $item['nombre_producto'] = $producto->item;
                $item['precio_producto'] = $producto->precio;
            } else {
                $item['nombre_producto'] = 'Producto no encontrado';
                $item['precio_producto'] = 0;
            }
        }

        $registroV->items = $items;

        return view('registro-v.show', compact('registroV'));
    }

    /**
     * Show the form for editing the specified resource. NO ME GUARDA ALMACEN 
     */
    public function edit($id): View
    {
        $registroV = RegistroV::find($id);

        $items = json_decode($registroV->items, true);

        foreach ($items as &$item) {
            $producto = RegistroV::find($item['producto']);
            if ($producto) {
                $item['nombre_producto'] = $producto->item;
            } else {
                $item['nombre_producto'] = 'Producto no encontrado';
            }
        }

        $registroV->items = $items;

        $clientes = Cliente::all();
        $inventario = Inventario::with('producto')->get();
        $almacenes = Almacene::all();

        return view('registro-v.edit', compact('registroV', 'clientes', 'inventario', 'almacenes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegistroVRequest $request, RegistroV $registroV): RedirectResponse
    {
        $registroV->update($request->validated());

        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        RegistroV::find($id)->delete();

        return Redirect::route('registro-vs.index')
            ->with('success', 'RegistroV deleted successfully');
    }
}
