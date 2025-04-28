<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioRequest;
use App\Models\Almacene;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Exports\InventariosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AjusteInventario;


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
    public function cargas(Request $request)
    {
        $cargas = AjusteInventario::with(['producto', 'almacene','user'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('inventario.cargas', [
            'cargas' => $cargas,
            'i' => ($request->input('page', 1) - 1) * $cargas->perPage()
        ]);
    }

    public function edit($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        return view('inventario.edit', compact('inventario')); // Changed from 'inventarios.edit' to 'inventario.edit'
    }

    /**
     * Actualizar cantidad con ajuste y registrar el ajuste.
     */
    public function update(Request $request, $id_inventario)
    {
        $request->validate([
            'tipo_ajuste' => 'required|in:compra,resta,ajuste,ajuste2',
            'cantidad_ajuste' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $inventario = Inventario::findOrFail($id_inventario);

        $cantidadAnterior = $inventario->cantidad;
        $cantidadNueva = $cantidadAnterior;

        // Calcular nueva cantidad según tipo de ajuste
        switch ($request->tipo_ajuste) {
            case 'compra':
            case 'ajuste':
                $cantidadNueva += $request->cantidad_ajuste;
                break;
            case 'ajuste2':
            case 'resta':
                $cantidadNueva -= $request->cantidad_ajuste;
                if ($cantidadNueva < 0) {
                    return back()->withErrors(['cantidad_ajuste' => 'La cantidad no puede ser negativa después del ajuste.']);
                }
                break;
        }

        // Actualizar inventario
        $inventario->cantidad = $cantidadNueva;
        $inventario->save();

        // Registrar ajuste en tabla ajustes_inventario
        AjusteInventario::create([
            'id_producto' => $inventario->id_producto,
            'id_almacen' => $inventario->id_almacen,
            'tipo_ajuste' => $request->tipo_ajuste,
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $cantidadNueva,
            'descripcion' => $request->descripcion,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('inventarios.edit', $inventario->id_inventario)
                         ->with('success', 'Inventario actualizado y ajuste registrado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Inventario::find($id)->delete();

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario eliminado satifactoriamente.');
    }
    
    public function export() 
    {
        return Excel::download(new InventariosExport, 'inventario.xlsx');
    }

  }

