<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CuotaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CuotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cuotas = Cuota::paginate();

        return view('cuota.index', compact('cuotas'))
            ->with('i', ($request->input('page', 1) - 1) * $cuotas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cuota = new Cuota();

        return view('cuota.create', compact('cuota'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CuotaRequest $request): RedirectResponse
    {
        Cuota::create($request->validated());

        return Redirect::route('cuotas.index')
            ->with('success', 'Cuota created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $cuota = Cuota::find($id);

        return view('cuota.show', compact('cuota'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $cuota = Cuota::find($id);

        return view('cuota.edit', compact('cuota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CuotaRequest $request, Cuota $cuota): RedirectResponse
    {
        $cuota->update($request->validated());

        return Redirect::route('cuotas.index')
            ->with('success', 'Cuota updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Cuota::find($id)->delete();

        return Redirect::route('cuotas.index')
            ->with('success', 'Cuota deleted successfully');
    }
}
