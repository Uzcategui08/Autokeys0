<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; 
use App\Models\Presupuesto; 
use App\Models\Inventario;
use App\Models\RegistroV;
use App\Models\AjusteInventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Datos generales (accesibles para todos)
        $producto = Producto::count();
        
        // Consultas que deben filtrarse por usuario si es limited
        $queryRegistros = RegistroV::query();
        $queryAjustes = AjusteInventario::query();
        
        if (auth()->user()->hasRole('limited')) {
            $userId = auth()->id();
            
            // Filtrar registros de ventas
            $queryRegistros->where('id_empleado', $userId);
            
            // Filtrar ajustes de inventario
            $queryAjustes->where('user_id', $userId);
        }

        // Estadísticas de ventas
        $suma_valor_v = $queryRegistros->clone()
            ->whereMonth('fecha_h', now()->month)
            ->whereYear('fecha_h', now()->year)
            ->sum('valor_v');

    $registros_mes_actual = $queryRegistros->clone()
        ->whereMonth('fecha_h', now()->month)
        ->whereYear('fecha_h', now()->year)
        ->get()
        ->sum(function($registro) {
            $items = json_decode($registro->items, true);
            // Verificamos si items es un array y luego contamos sus elementos
            return is_array($items) ? count($items) : 0;
        });
        
        $registros_mes_anterior = $queryRegistros->clone()
            ->whereMonth('fecha_h', now()->subMonth()->month)
            ->whereYear('fecha_h', now()->subMonth()->year)
            ->count();

        // Cálculo de diferencia porcentual
        $diferencia = 0;
        if ($registros_mes_anterior > 0) {
            $diferencia = (($registros_mes_actual - $registros_mes_anterior) / $registros_mes_anterior) * 100;
        } elseif ($registros_mes_actual > 0) {
            $diferencia = 100; 
        }

        // Ventas por lugar (solo para admin)
        $ventasPorLugar = collect();
        if (!auth()->user()->hasRole('limited')) {
            $ventasPorLugar = RegistroV::select(
                    DB::raw('EXTRACT(MONTH FROM fecha_h) as mes'),
                    'lugarventa',
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('fecha_h', Carbon::now()->year)
                ->groupBy('mes', 'lugarventa')
                ->orderBy('mes')
                ->get();
        }

        // Ventas por técnico (modificado para limited)
        $ventasPorTecnicoQuery = RegistroV::query();
        
        if (auth()->user()->hasRole('limited')) {
            $ventasPorTecnicoQuery->where('id_empleado', auth()->id());
        }
        
        $ventasPorTecnico = $ventasPorTecnicoQuery
        ->with('empleado') 
        ->select(
            'id_empleado',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(valor_v) as monto_total')
        )
        ->groupBy('id_empleado')
        ->orderBy('id_empleado')
        ->get()
        ->map(function ($item) {
            return (object) [
                'tecnico' => $item->empleado->nombre,
                'total' => $item->total,
                'monto_total' => $item->monto_total
            ];
        });

        // Estadísticas de inventario para limited
        $misAjustesInventario = null;
        if (auth()->user()->hasRole('limited')) {
            $misAjustesInventario = $queryAjustes
                ->whereYear('created_at', now()->year)
                ->select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as mes'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(diferencia) as cantidad_total')
                )
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();
        }
        
        return view('dashboard', [
            'productos' => $producto,
            'registros' => $registros_mes_actual,
            'diferencia_porcentual' => round($diferencia, 2),
            'valorV' => round($suma_valor_v),
            'ventasPorLugar' => $ventasPorLugar,
            'ventasPorTecnico' => $ventasPorTecnico,
            'misAjustesInventario' => $misAjustesInventario
        ]);
    }
}