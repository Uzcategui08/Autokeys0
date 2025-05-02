<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroV;
use App\Models\Empleado;
use Illuminate\Support\Facades\DB;

class CierreVentasController extends Controller
{
    public function index(Request $request)
    {
        // Obtener técnicos desde la tabla empleados (asumiendo tipo 1 son técnicos)
        $tecnicos = Empleado::where('tipo', 2) // Filtra solo técnicos si es necesario
            ->orderBy('nombre')
            ->pluck('nombre', 'id_empleado');
        
        $tecnicoIdSeleccionado = $request->input('tecnico');
        $ventasQuery = RegistroV::query();
        
        if ($tecnicoIdSeleccionado) {
            // Obtener el nombre del técnico para filtrar en registroV
            $tecnico = Empleado::find($tecnicoIdSeleccionado);
            if ($tecnico) {
                $ventasQuery->where('tecnico', $tecnico->nombre);
            }
        }
        
        $ventas = $ventasQuery->get();
        
        $totales = [
            'total_general' => 0,
            'total_pagado' => 0,
            'total_credito' => 0,
        ];
        
        foreach ($ventas as $venta) {
            $totales['total_general'] += $venta->valor_v;
            
            $pagos = json_decode($venta->pagos, true) ?? [];
            $totalPagado = array_reduce($pagos, function($total, $pago) {
                return $total + (float)($pago['monto'] ?? 0);
            }, 0);
            
            $totales['total_pagado'] += $totalPagado;
            $totales['total_credito'] += ($venta->valor_v - $totalPagado);
        }
        
        return view('estadisticas.cierre.index', [
            'tecnicos' => $tecnicos,
            'tecnicoSeleccionado' => $tecnicoIdSeleccionado,
            'ventas' => $ventas,
            'totales' => $totales
        ]);
    }
    
    // Método alternativo más eficiente para muchos registros
    public function resumenTecnicos()
    {
        // Obtener primero todos los técnicos
        $tecnicos = Empleado::where('tipo', 1)->get();
        
        $resumen = [];
        
        foreach ($tecnicos as $tecnico) {
            $ventas = RegistroV::where('tecnico', $tecnico->nombre)
                ->select(
                    DB::raw('SUM(valor_v) as total_general'),
                    DB::raw('SUM(
                        CASE 
                            WHEN estatus = "pagado" THEN valor_v
                            WHEN estatus = "parcialemente pagado" THEN 
                                (SELECT SUM(JSON_EXTRACT(p.monto, "$")) 
                                FROM JSON_TABLE(pagos, "$[*]" COLUMNS(
                                    monto DECIMAL(10,2) PATH "$.monto"
                                )) as p
                            ELSE 0
                        END
                    ) as total_pagado')
                )
                ->first();
            
            if ($ventas) {
                $resumen[] = [
                    'tecnico' => $tecnico->nombre,
                    'total_general' => $ventas->total_general ?? 0,
                    'total_pagado' => $ventas->total_pagado ?? 0,
                    'total_credito' => ($ventas->total_general ?? 0) - ($ventas->total_pagado ?? 0)
                ];
            }
        }
        
        return view('estadisticas.cierre.resumen', ['resumen' => $resumen]);
    }
}