<?php
namespace App\Http\Controllers;

use App\Models\RegistroV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CierreVentasController extends Controller
{
    public function index()
    {
        // Obtener el conteo de trabajos agrupados
        $conteoTrabajos = RegistroV::select('trabajo', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('trabajo')
            ->orderBy('trabajo')
            ->get();
        
        // Calcular el total general de trabajos
        $totalGeneral = $conteoTrabajos->sum('cantidad');
        
        // Obtener el total de ventas por almacén (lugarventa)
        $ventasPorAlmacen = RegistroV::select(
                'lugarventa',
                DB::raw('SUM(valor_v) as total_ventas'),
                DB::raw('COUNT(*) as cantidad_ventas')
            )
            ->groupBy('lugarventa')
            ->orderBy('lugarventa')
            ->get();
            
        // Calcular el total general de ventas
        $totalVentas = $ventasPorAlmacen->sum('total_ventas');
            
        return view('estadisticas.cierre', [
            'conteoTrabajos' => $conteoTrabajos,
            'totalGeneral' => $totalGeneral,
            'ventasPorAlmacen' => $ventasPorAlmacen,
            'totalVentas' => $totalVentas
        ]);
    }
}