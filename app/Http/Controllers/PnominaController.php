<?php

namespace App\Http\Controllers;

use App\Models\Pnomina;
use App\Models\Tnomina;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PnominaRequest;
use Illuminate\Support\Facades\Redirect;
use App\Events\PeriodoCreado;
use Illuminate\View\View;

class PnominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pnominas = Pnomina::with('tnomina')->get();
        
        return view('pnomina.index', compact('pnominas'))
            ->with('i', 0); 
    }
       
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $pnomina = new Pnomina();
        $tnominas = Tnomina::all();

        return view('pnomina.create', compact('pnomina', 'tnominas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PnominaRequest $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_tnomina' => 'required|exists:tnominas,id_tnomina',
            'inicio' => 'required|date',
            'fin' => 'required|date|after_or_equal:inicio',
            'metodos_pago' => 'required|array',
            'metodos_pago.*' => 'required|in:1,2,3,4'
        ]);
    
        $periodo = Pnomina::create([
            'id_tnomina' => $validated['id_tnomina'],
            'inicio' => $validated['inicio'],
            'fin' => $validated['fin'],
            'activo' => true 
        ]);
        event(new PeriodoCreado($periodo, $validated['metodos_pago']));
    
        return Redirect::route('pnominas.index')
            ->with('success', 'Pnomina created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $pnomina = Pnomina::find($id);

        return view('pnomina.show', compact('pnomina'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $pnomina = Pnomina::find($id);
        $tnominas = Tnomina::all();

        return view('pnomina.edit', compact('pnomina', 'tnominas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PnominaRequest $request, Pnomina $pnomina): RedirectResponse
    {
        $pnomina->update($request->validated());

        return Redirect::route('pnominas.index')
            ->with('success', 'Pnomina updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Pnomina::find($id)->delete();

        return Redirect::route('pnominas.index')
            ->with('success', 'Pnomina deleted successfully');
    }
}
