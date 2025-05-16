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
         $query = Inventario::with(['producto', 'almacene']);
             
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
    if (auth()->user()->hasRole('limited')) {
        $query->where('user_id', auth()->id());
    }
    
    // Obtener todos los registros sin paginación
    $cargas = $query->get();
    
    return view('inventario.cargas', [
        'cargas' => $cargas,
        'i' => 0 // Como no hay paginación, el índice comienza en 0
    ]);
}
    public function edit($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        return view('inventario.edit', compact('inventario'));
    }

    /**
     * Actualización básica
     */
    public function update(Request $request, $id_inventario)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $inventario = Inventario::findOrFail($id_inventario);
        $inventario->update(['cantidad' => $request->cantidad]);

        return redirect()->route('inventarios.index')
             ->with('success', 'Inventario actualizado correctamente');
    }

    /**
     * Edición con sistema de ajustes (nuevo formulario avanzado)
     */
    public function editarConAjustes($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        $productos = Producto::select('id_producto', 'item',)->get();
        $almacenes = Almacene::select('id_almacen', 'nombre')->get();
        
        return view('inventario.ajustar', compact('inventario', 'productos', 'almacenes'));
    }

    /**
     * Actualización con sistema de ajustes
     */
    public function actualizarConAjustes(Request $request, $id_inventario)
    {
        $request->validate([
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:500',
        ]);

        $inventario = Inventario::findOrFail($id_inventario);
        $cantidadAnterior = $inventario->cantidad;
        $cantidadAjuste = $request->cantidad;

        // Calcular nueva cantidad
        switch ($request->tipo_movimiento) {
            case 'entrada':
                $nuevaCantidad = $cantidadAnterior + $cantidadAjuste;
                break;
            case 'salida':
                $nuevaCantidad = $cantidadAnterior - $cantidadAjuste;
                if ($nuevaCantidad < 0) {
                    return back()->withErrors(['cantidad' => 'La cantidad resultante no puede ser negativa']);
                }
                break;
            case 'ajuste':
                $nuevaCantidad = $cantidadAjuste;
                break;
        }

        // Actualizar inventario
        $inventario->update(['cantidad' => $nuevaCantidad]);

        // Registrar el ajuste
        AjusteInventario::create([
            'id_inventario' => $inventario->id_inventario,
            'id_producto' => $inventario->id_producto,
            'id_almacen' => $inventario->id_almacen,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_ajuste' => $cantidadAjuste,
            'nueva_cantidad' => $nuevaCantidad,
            'motivo' => $request->motivo,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('inventarios.index')
             ->with('success', 'Inventario ajustado correctamente. Se registró el movimiento.');
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

