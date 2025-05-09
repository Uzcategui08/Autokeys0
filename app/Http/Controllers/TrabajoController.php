<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TrabajoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TrabajoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $trabajos = Trabajo::paginate();

        return view('trabajo.index', compact('trabajos'))
            ->with('i', ($request->input('page', 1) - 1) * $trabajos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $trabajo = new Trabajo();

        return view('trabajo.create', compact('trabajo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrabajoRequest $request): RedirectResponse
    {
        Trabajo::create($request->validated());

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $trabajo = Trabajo::find($id);

        return view('trabajo.show', compact('trabajo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $trabajo = Trabajo::find($id);

        return view('trabajo.edit', compact('trabajo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrabajoRequest $request, Trabajo $trabajo): RedirectResponse
    {
        $trabajo->update($request->validated());

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Trabajo::find($id)->delete();

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo eliminado satisfactoriamente.');
    }
}
