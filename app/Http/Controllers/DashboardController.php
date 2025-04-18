<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Producto; // Asegúrate de importar el modelo Producto
    use App\Models\Presupuesto; // Asumo que necesitas estos modelos también
    use App\Models\Inventario;
    use DB; // Necesario para usar DB::raw()
    
    class DashboardController extends Controller
    {
        public function index()
        {
            $producto = Producto::count();
            
            return view('dashboard', [
                'productos' => $producto, 

            ]);
        }
    }
    


