<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Empleado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class GastoController extends Controller
{
    public function index(Request $request): View
    {
        $gastos = Gasto::paginate(10);
        return view('gasto.index', compact('gastos'))
            ->with('i', ($request->input('page', 1) - 1) * $gastos->perPage());
    }

    public function create(): View
    {
        $gasto = new Gasto();
        $empleado = Empleado::where('cargo', '1')->get();
        $gasto->f_gastos = now()->format('Y-m-d');
        $gasto->estatus = 'pendiente';
        return view('gasto.create', compact('gasto', 'empleado'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_gastos' => 'required|date',
                'id_tecnico' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required|string|in:mantenimiento,repuestos,herramientas,software,consumibles,combustible,capacitacion,otros',
                'valor' => 'required|numeric|min:0',
                'estatus' => 'required|in:pendiente,parcialmente_pagado,pagado',
            ]);

            $pagosData = [];
            if ($request->has('pagos')) {
                $pagosInput = $request->input('pagos');
                if (is_string($pagosInput)) {
                    $pagosData = json_decode(trim($pagosInput, '"\' '), true) ?? [];
                } elseif (is_array($pagosInput)) {
                    $pagosData = $pagosInput;
                }
            }

            $gasto = new Gasto([
                'f_gastos' => $validated['f_gastos'],
                'id_tecnico' => $validated['id_tecnico'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'estatus' => $validated['estatus'],
                'pagos' => $pagosData
            ]);

            if (!$gasto->save()) {
                throw new \Exception("No se pudo guardar el registro en la base de datos");
            }

            return Redirect::route('gastos.index')
                ->with('success', 'Gasto creado satisfactoriamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al guardar el gasto: ' . $e->getMessage()]);
        }
    }

    public function show($id): View
    {
        $gasto = Gasto::findOrFail($id);
        return view('gasto.show', [
            'gasto' => $gasto,
            'total_pagado' => $this->calcularTotalPagado($gasto->pagos),
            'saldo_pendiente' => $gasto->valor - $this->calcularTotalPagado($gasto->pagos)
        ]);
    }

    public function edit($id): View
    {
        $gasto = Gasto::findOrFail($id);
        $empleado = Empleado::where('cargo', '1')->get();
        return view('gasto.edit', compact('gasto', 'empleado'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_gastos' => 'required|date',
                'id_tecnico' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required|string|in:mantenimiento,repuestos,herramientas,software,consumibles,combustible,capacitacion,otros',
                'valor' => 'required|numeric|min:0',
                'pagos' => 'required|json'
            ]);

            $pagosJson = trim($validated['pagos'], '"\'');
            $pagos = json_decode($pagosJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($pagos)) {
                throw new \Exception("Formato de pagos inválido: " . json_last_error_msg());
            }

            $gasto = Gasto::findOrFail($id);
            $totalPagado = $this->calcularTotalPagado($pagos);
            $estatus = $this->determinarEstatus($validated['valor'], $pagos);

            $gasto->update([
                'f_gastos' => $validated['f_gastos'],
                'id_tecnico' => $validated['id_tecnico'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'pagos' => $pagos,
                'estatus' => $estatus
            ]);

            return Redirect::route('gastos.index')
                ->with('success', 'Gasto actualizado satisfactoriamente.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function calcularTotalPagado(array $pagos): float
    {
        if (empty($pagos)) return 0;
        return array_sum(array_column($pagos, 'monto'));
    }

    private function determinarEstatus(float $valor, array $pagos): string
    {
        $totalPagado = $this->calcularTotalPagado($pagos);
        
        if (abs($totalPagado - $valor) < 0.01) {
            return 'pagado';
        } elseif ($totalPagado > 0) {
            return 'parcialmente_pagado';
        }
        return 'pendiente';
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $gasto = Gasto::findOrFail($id);
            $gasto->delete();
            return Redirect::route('gastos.index')
                ->with('success', 'Gasto eliminado satisfactoriamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al eliminar el gasto']);
        }
    }
}