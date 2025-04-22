<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Producto; 
    use App\Models\Presupuesto; 
    use App\Models\Inventario;
    use App\Models\RegistroV;
    use Carbon\Carbon;
    
    class DashboardController extends Controller
    {
        public function index()
        {
            {
                $producto = Producto::count();
                
                $suma_valor_v = RegistroV::whereMonth('fecha_h', now()->month)
                    ->whereYear('fecha_h', now()->year)
                    ->sum('valor_v');

                $registros_mes_actual = RegistroV::whereMonth('fecha_h', now()->month)
                    ->whereYear('fecha_h', now()->year)
                    ->count();
                
                $registros_mes_anterior = RegistroV::whereMonth('fecha_h', now()->subMonth()->month)
                    ->whereYear('fecha_h', now()->subMonth()->year)
                    ->count();

                $diferencia = 0;
                if ($registros_mes_anterior > 0) {
                    $diferencia = (($registros_mes_actual - $registros_mes_anterior) / $registros_mes_anterior) * 100;
                } elseif ($registros_mes_actual > 0) {
                    $diferencia = 100; 
                }
                
                return view('dashboard', [
                    'productos' => $producto,
                    'registros' => $registros_mes_actual,
                    'diferencia_porcentual' => round($diferencia, 2),
                    'valorV' => round($suma_valor_v)
                ]);
            }
        }
    }


