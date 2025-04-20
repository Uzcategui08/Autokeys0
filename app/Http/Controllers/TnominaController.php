<?php

namespace App\Http\Controllers;

use App\Models\Tnomina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TnominaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TnominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tnominas = Tnomina::paginate();

        return view('tnomina.index', compact('tnominas'))
            ->with('i', ($request->input('page', 1) - 1) * $tnominas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tnomina = new Tnomina();

        return view('tnomina.create', compact('tnomina'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TnominaRequest $request): RedirectResponse
    {
        Tnomina::create($request->validated());

        return Redirect::route('tnominas.index')
            ->with('success', 'Tnomina created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $tnomina = Tnomina::find($id);

        return view('tnomina.show', compact('tnomina'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $tnomina = Tnomina::find($id);

        return view('tnomina.edit', compact('tnomina'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TnominaRequest $request, Tnomina $tnomina): RedirectResponse
    {
        $tnomina->update($request->validated());

        return Redirect::route('tnominas.index')
            ->with('success', 'Tnomina updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Tnomina::find($id)->delete();

        return Redirect::route('tnominas.index')
            ->with('success', 'Tnomina deleted successfully');
    }
}
