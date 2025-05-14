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
use Illuminate\Support\Facades\Log;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function getData(Request $request)
     {
         $query = Inventario::with(['producto', 'almacene'])
             ->where('cantidad', '>=', 1);
             
         if ($request->has('almacen_id') && $request->almacen_id) {
             $query->where('id_almacen', $request->almacen_id);
         }
         
         $inventarios = $query->get();
         
         return response()->json([
             'data' => $inventarios
         ]);
     }

    public function index(Request $request): View
    {
        $inventarios1 = Inventario::with(['producto', 'almacene'])
            ->where('cantidad', '>=', 1) 
            ->orderByRaw('CAST(cantidad AS NUMERIC) ASC')
            ->get();
    
        $almacenes = Almacene::all();
    
        return view('inventario.index', compact('inventarios1', 'almacenes'));
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
        // Carga el inventario con sus relaciones y maneja el caso de no encontrado
        $inventario = Inventario::with(['producto', 'almacene'])
                      ->findOrFail($id);
    
        return view('inventario.show', compact('inventario'));
    }
    
    public function cargas(Request $request)
    {
        $query = AjusteInventario::with(['producto', 'almacene', 'user'])
                    ->orderBy('created_at', 'desc');
        
        // Si el usuario es limited_user, filtrar solo sus cargas
        if (auth()->user()->hasRole('limited_user')) {
            $query->where('user_id', auth()->id());
        }
        
        $cargas = $query->all();
        
        return view('inventario.cargas', [
            'cargas' => $cargas,
        ]);
    }

    public function edit($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        return view('inventario.edit', compact('inventario')); 
    }

    /**
     * 
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

        return redirect()->route('inventarios.index', $inventario->id_inventario)
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

