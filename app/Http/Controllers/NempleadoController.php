<?php

namespace App\Http\Controllers;

use App\Models\Nempleado;
use App\Models\Pnomina;
use App\Models\Empleado;
use App\Models\Cuota;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\NempleadoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class NempleadoController extends Controller
{
    public function index(Request $request): View
    {
        $nempleados = Nempleado::paginate();
        return view('nempleado.index', compact('nempleados'))
            ->with('i', ($request->input('page', 1) - 1) * $nempleados->perPage());
    }

    public function create(): View
    {
        $nempleado = new Nempleado();
        return view('nempleado.create', compact('nempleado'));
    }

    public function store(NempleadoRequest $request): RedirectResponse
    {
        Nempleado::create($request->validated());
        return Redirect::route('nempleados.index')
            ->with('success', 'Nempleado created successfully.');
    }

    public function show($id): View
    {
        $nempleado = Nempleado::find($id);
        return view('nempleado.show', compact('nempleado'));
    }

    public function edit($id): View
    {
        $nempleado = Nempleado::find($id);
        return view('nempleado.edit', compact('nempleado'));
    }

    public function update(NempleadoRequest $request, Nempleado $nempleado): RedirectResponse
    {
        $nempleado->update($request->validated());
        return Redirect::route('nempleados.index')
            ->with('success', 'Nempleado updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Nempleado::find($id)->delete();
        return Redirect::route('nempleados.index')
            ->with('success', 'Nempleado deleted successfully');
    }

    protected function calcularFactor($frecuencia)
    {
        return match ($frecuencia) {
            1 => 0.5,
            2 => 1,
            3 => 0.25,
            default => 0
        };
    }

    public function generarRecibo($periodoId, $empleadoId)
    {
        try {
            $periodo = Pnomina::with('tipo')->findOrFail($periodoId);
            $empleado = Empleado::with('tipoNomina')->findOrFail($empleadoId);
            
            $factor = $this->calcularFactor($periodo->tipo->frecuencia);
            $totalPagado = $empleado->salario_base * $factor;
        
            $nominaEmpleado = Nempleado::where('id_pnomina', $periodoId)
                ->where('id_empleado', $empleadoId)
                ->firstOrFail();
        
            $descuentosCollection = $empleado->descuentos()
                ->whereBetween('d_fecha', [$periodo->inicio, $periodo->fin])
                ->get();
                
            $abonosCollection = $empleado->abonos()
                ->whereBetween('a_fecha', [$periodo->inicio, $periodo->fin])
                ->get();

            $costosCollection = $empleado->costos()
                ->whereBetween('f_costos', [$periodo->inicio, $periodo->fin])
                ->get();    
        
            $prestamosSimplificados = [];
            
            if ($nominaEmpleado->total_prestamos > 0) {
                $prestamosActivos = $empleado->prestamos()
                    ->where('activo', true)
                    ->get();
                
                foreach ($prestamosActivos as $prestamo) {
                    $prestamosSimplificados[] = [
                        'numero_prestamo' => $prestamo->id_prestamos,
                        'cuota_actual' => $prestamo->cuota_actual,
                        'monto_prestamo' => $prestamo->valor/$prestamo->cuotas
                    ];
                }
            }
        
            $data = [
                'periodo' => $periodo,
                'empleado' => $empleado,
                'factor' => $factor,
                'totalPagado' => $totalPagado,
                'totalDescuentos' => $nominaEmpleado->total_descuentos,
                'totalAbonos' => $nominaEmpleado->total_abonos,
                'totalCostos' => $nominaEmpleado->total_costos,
                'totalPrestamos' => $nominaEmpleado->total_prestamos,
                'netoPagado' => $nominaEmpleado->total_pagado,
                'costos' => $costosCollection,
                'descuentos' => $descuentosCollection,
                'abonos' => $abonosCollection,
                'prestamos' => $prestamosSimplificados
            ];
            
            return PDF::loadView('nempleado.pdf', $data)->stream('recibo.pdf');
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generarReciboGeneral($periodoId)
    {
        try {
            $periodo = Pnomina::with('tipo')->findOrFail($periodoId);

            $nominasEmpleados = Nempleado::with(['empleado' => function($query) {
                    $query->with('tipoNomina');
                }])
                ->where('id_pnomina', $periodoId)
                ->get();

            $totales = [
                'totalPagado' => 0,
                'totalDescuentos' => 0,
                'totalCostos' => 0,
                'totalAbonos' => 0,
                'totalPrestamos' => 0,
                'netoPagado' => 0
            ];

            $empleadosData = [];
            foreach ($nominasEmpleados as $nominaEmpleado) {
                $empleado = $nominaEmpleado->empleado;
                
                $factor = $this->calcularFactor($periodo->tipo->frecuencia);
                $totalPagado = $empleado->salario_base * $factor;

                $prestamos = [];
                if ($nominaEmpleado->total_prestamos > 0) {
                    $prestamosActivos = $empleado->prestamos()
                        ->where('activo', true)
                        ->get();

                    foreach ($prestamosActivos as $prestamo) {
                        $prestamos[] = [
                            'numero' => $prestamo->id_prestamos,
                            'cuota' => $prestamo->cuota_actual,
                            'valor' => $prestamo->valor_cuota
                        ];
                    }
                }

                $empleadoData = [
                    'id' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre,
                    'cedula' => $empleado->cedula,
                    'cargo' => $empleado->cargo,
                    'salario_base' => $empleado->salario_base,
                    'factor' => $factor,
                    'totalPagado' => $totalPagado,
                    'totalDescuentos' => $nominaEmpleado->total_descuentos,
                    'totalCostos' => $nominaEmpleado->total_costos,
                    'totalAbonos' => $nominaEmpleado->total_abonos,
                    'totalPrestamos' => $nominaEmpleado->total_prestamos,
                    'netoPagado' => $nominaEmpleado->total_pagado,
                    'prestamos' => $prestamos
                ];

                $totales['totalPagado'] += $totalPagado;
                $totales['totalDescuentos'] += $nominaEmpleado->total_descuentos;
                $totales['totalCostos'] += $nominaEmpleado->total_costos;
                $totales['totalAbonos'] += $nominaEmpleado->total_abonos;
                $totales['totalPrestamos'] += $nominaEmpleado->total_prestamos;
                $totales['netoPagado'] += $nominaEmpleado->total_pagado;

                $empleadosData[] = $empleadoData;
            }

            $data = [
                'periodo' => $periodo,
                'empleados' => $empleadosData,
                'totales' => $totales,
                'fechaGeneracion' => now()->format('d/m/Y H:i:s')
            ];

            return PDF::loadView('nempleado.pdfgeneral', $data)
                    ->stream('nomina_general_'.$periodoId.'.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte general');
        }
    }


    public function reporte(Request $request)
    {
        $periodos = Pnomina::with('tnomina')->orderBy('inicio', 'desc')->get();
        $empleados = Empleado::orderBy('nombre')->get();
        
        $nominasIndividuales = collect();
        $nominaGeneral = null;
        
        if ($request->filled('periodo_id')) {
            $periodoId = $request->periodo_id;

            if ($request->filled('tipo') && $request->tipo == 'individual') {
                $query = Nempleado::with(['empleado', 'pnomina.tipo'])
                    ->where('id_pnomina', $periodoId);
                    
                if ($request->filled('empleado_id')) {
                    $query->where('id_empleado', $request->empleado_id);
                }
                
                $nominasIndividuales = $query->get();
            }
            elseif ($request->filled('tipo') && $request->tipo == 'general') {
                $periodo = Pnomina::find($periodoId);
                
                $nominaGeneral = [
                    'periodo' => $periodo,
                    'total_empleados' => Nempleado::where('id_pnomina', $periodoId)->count(),
                    'total_nomina' => Nempleado::where('id_pnomina', $periodoId)->sum('total_pagado')
                ];
            }
        }
        
        if ($request->ajax()) {
            return view('nempleado.reporte', compact('periodos', 'empleados', 'nominasIndividuales', 'nominaGeneral'));
        }
        
        return view('nempleado.reporte', compact('periodos', 'empleados', 'nominasIndividuales', 'nominaGeneral'));
    }
}