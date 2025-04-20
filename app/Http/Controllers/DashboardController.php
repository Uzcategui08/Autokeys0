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
            $producto = Producto::count();
            
            // Registros del mes actual
            $registros_mes_actual = RegistroV::whereMonth('fecha_h', now()->month)
                ->whereYear('fecha_h', now()->year)
                ->count();
    
            return view('dashboard', [
                'productos' => $producto,
                'registros' => $registros_mes_actual,
            ]);
        }
    }


