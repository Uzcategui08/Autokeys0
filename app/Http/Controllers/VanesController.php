<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto; 
use App\Models\Costo;
use App\Models\RegistroV;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VanesController extends Controller
{
    public function index()
    {
        // Definir los lugares de venta
        $vanGrande = 'Van Grande-Pulga'; 
        $vanPequena = 'Van Pequeña-Pulga'; 
        
        // Obtener registros de venta por van con sus fechas únicas
        $ventasVanGrande = RegistroV::where('lugarventa', $vanGrande)
                                  ->orderBy('fecha_h', 'desc')
                                  ->get();
        
        $ventasVanPequena = RegistroV::where('lugarventa', $vanPequena)
                                   ->orderBy('fecha_h', 'desc')
                                   ->get();
        
        // Obtener array de fechas únicas para cada van
        $fechasVanGrande = $ventasVanGrande->pluck('fecha_h')->map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->unique()->toArray();
        
        $fechasVanPequena = $ventasVanPequena->pluck('fecha_h')->map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->unique()->toArray();
        
        // Filtrar gastos que coincidan con fechas de ventas
        $gastosVanGrande = Gasto::where('subcategoria', 'gasto_extra')
                               ->whereIn(DB::raw('DATE(f_gastos)'), $fechasVanGrande)
                               ->orderBy('f_gastos', 'desc')
                               ->get();
                               
        $gastosVanPequena = Gasto::where('subcategoria', 'gasto_extra')
                                ->whereIn(DB::raw('DATE(f_gastos)'), $fechasVanPequena)
                                ->orderBy('f_gastos', 'desc')
                                ->get();
        
        // Filtrar costos que coincidan con fechas de ventas
        $costosVanGrande = Costo::where('subcategoria', 'costo_extra')
                              ->whereIn(DB::raw('DATE(f_costos)'), $fechasVanGrande)
                              ->orderBy('f_costos', 'desc')
                              ->get();
                              
        $costosVanPequena = Costo::where('subcategoria', 'costo_extra')
                               ->whereIn(DB::raw('DATE(f_costos)'), $fechasVanPequena)
                               ->orderBy('f_costos', 'desc')
                               ->get();
                               $porcentajeCerrajeroGrande = 0; // nombre columna porcentaje cerrajero
                               $porcentajeCerrajeroPequena = 0;
                               
        // Calcular totales
        $totales = [
            'porcentajeCerrajeroGrande' => $ventasVanGrande->sum('porcentaje_c'), 
            'porcentajeCerrajeroPequena' => $ventasVanPequena->sum('porcentaje_c'),
            'ventasGrande' => $ventasVanGrande->sum('valor_v'),
            'ventasPequena' => $ventasVanPequena->sum('valor_v'),
            'gastosGrande' => $gastosVanGrande->sum('valor'),
            'gastosPequena' => $gastosVanPequena->sum('valor'),
            'costosGrande' => $costosVanGrande->sum('valor'),
            'costosPequena' => $costosVanPequena->sum('valor'),
        ];
        
        return view('estadisticas.vanes', compact(
            'porcentajeCerrajeroPequena',
            'porcentajeCerrajeroGrande',
            'vanGrande',
            'vanPequena',
            'ventasVanGrande',
            'ventasVanPequena',
            'gastosVanGrande',
            'gastosVanPequena',
            'costosVanGrande',
            'costosVanPequena',
            'totales'
        ));
    
    }
}