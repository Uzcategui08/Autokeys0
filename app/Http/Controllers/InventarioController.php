<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioRequest;
use App\Models\Almacene;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $inventarios = Inventario::paginate();
        $inventarios1 = Inventario::with(['producto', 'almacene'])->get();

        return view('inventario.index', compact('inventarios', 'inventarios1'))
            ->with('i', ($request->input('page', 1) - 1) * $inventarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Obtén todos los productos y almacenes
        $productos = Producto::all();
        $almacenes = Almacene::all();
        $inventario = new Inventario();

        // Pasa los datos a la vista
        return view('inventario.create', compact('inventario', 'productos', 'almacenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventarioRequest $request): RedirectResponse
    {
        Inventario::create($request->validated());

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $inventario = Inventario::find($id);

        return view('inventario.show', compact('inventario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        // Obtén el inventario a editar
        $inventario = Inventario::findOrFail($id);

        // Obtén todos los productos y almacenes
        $productos = Producto::all();
        $almacenes = Almacene::all();

        // Pasa los datos a la vista
        return view('inventario.edit', compact('inventario', 'productos', 'almacenes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventarioRequest $request, Inventario $inventario): RedirectResponse
    {
        $inventario->update($request->validated());

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Inventario::find($id)->delete();

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario eliminado satifactoriamente.');
    }
}
