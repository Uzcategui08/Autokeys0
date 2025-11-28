<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use Illuminate\Http\Request;

class DevolucionController extends Controller
{
    public function index()
    {
        $devoluciones = Devolucion::orderByDesc('fecha')->paginate(15);

        return view('devoluciones.index', compact('devoluciones'));
    }

    public function create()
    {
        return view('devoluciones.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Devolucion::create($validated);

        return redirect()
            ->route('devoluciones.index')
            ->with('success', 'Devolución registrada correctamente.');
    }

    public function edit(Devolucion $devolucion)
    {
        return view('devoluciones.edit', compact('devolucion'));
    }

    public function update(Request $request, Devolucion $devolucion)
    {
        $validated = $this->validateData($request);

        $devolucion->update($validated);

        return redirect()
            ->route('devoluciones.index')
            ->with('success', 'Devolución actualizada correctamente.');
    }

    public function destroy(Devolucion $devolucion)
    {
        $devolucion->delete();

        return redirect()
            ->route('devoluciones.index')
            ->with('success', 'Devolución eliminada correctamente.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'fecha' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['required', 'string', 'max:255'],
        ]);
    }
}
