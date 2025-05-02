<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Gasto;
use App\Models\Costo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticasVentasController extends Controller
{
    protected $month;
    protected $year;
    protected $availableYears;

    public function __construct()
    {
        // Obtener años disponibles para el filtro
        $this->availableYears = $this->getAvailableYears();
    }

    public function index(Request $request)
    {
        $this->month = $request->input('month', date('m'));
        $this->year = $request->input('year', date('Y'));
        
        // Verificar si hay datos de ventas para el mes seleccionado
        $hasData = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->exists();
        
        $stats = $hasData ? $this->getAllStats() : null;
        
        return view('estadisticas.ventas', [
            'stats' => $stats,
            'monthSelected' => $this->month,
            'yearSelected' => $this->year,
            'availableYears' => $this->availableYears,
            'noData' => !$hasData // Pasamos explícitamente si no hay datos
        ]);
    }

    protected function getAvailableYears()
    {
        // Obtener años únicos de todas las tablas relevantes
        $yearsRegistroV = RegistroV::selectRaw('YEAR(fecha_h) as year')
            ->distinct()
            ->pluck('year');
        
        $yearsGastos = Gasto::selectRaw('YEAR(f_gastos) as year')
            ->distinct()
            ->pluck('year');
            
        $yearsCostos = Costo::selectRaw('YEAR(f_costos) as year')
            ->distinct()
            ->pluck('year');
            
        // Combinar y obtener años únicos
        $allYears = $yearsRegistroV->merge($yearsGastos)->merge($yearsCostos)->unique()->sortDesc();
        
        return $allYears->values()->all();
    }

    // Métodos para estadísticas de ventas
    protected function cobradoDelMes()
    {
        $registros = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get();

        $total = 0;
        foreach ($registros as $registro) {
            if ($registro->pagos) {
                foreach ($registro->pagos as $pago) {
                    $total += $pago['monto'];
                }
            }
        }
        return $total;
    }

    protected function facturacionDelMes()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->sum('valor_v');
    }

    protected function evolucionFacturacion()
    {
        $currentMonthFact = $this->facturacionDelMes();
        $lastMonth = Carbon::create($this->year, $this->month, 1)->subMonth();
        $lastMonthFact = (new self($lastMonth->month, $lastMonth->year))->facturacionDelMes();
        return $lastMonthFact == 0 ? 0 : ($currentMonthFact / $lastMonthFact) * 100;
    }

    protected function numeroTransacciones()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->count();
    }

    protected function ticketPromedio()
    {
        $transacciones = $this->numeroTransacciones();
        return $transacciones == 0 ? 0 : $this->facturacionDelMes() / $transacciones;
    }

    // Métodos para costos y gastos
    protected function totalCostoVenta()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->sum('monto_ce');
    }

    protected function totalGastoPersonal()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'personal')
            ->sum('valor');
    }

    protected function totalGastosOperativos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'operativos')
            ->sum('valor');
    }

    protected function totalOtrosGastos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'otros')
            ->sum('valor');
    }

    protected function totalFinancierosImpuestos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'financieros_impuestos')
            ->sum('valor');
    }

    protected function totalGastos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->sum('valor');
    }

    // Métodos auxiliares
    protected function calcularPorcentaje($valor, $facturacion)
    {
        return $facturacion == 0 ? 0 : ($valor / $facturacion) * 100;
    }

    protected function calcularUtilidadBruta()
    {
        return $this->facturacionDelMes() - $this->totalCostoVenta();
    }

    protected function calcularUtilidadNeta()
    {
        return $this->calcularUtilidadBruta() - $this->totalGastos();
    }

    // Método principal que obtiene todas las estadísticas
    protected function getAllStats()
    {
        $facturacion = $this->facturacionDelMes();
        $utilidadBruta = $this->calcularUtilidadBruta();
        $utilidadNeta = $this->calcularUtilidadNeta();

        return [
            // Datos básicos
            'month' => $this->month,
            'year' => $this->year,
            
            // Estadísticas de ventas
            'ventas' => [
                'cobrado_mes' => $this->cobradoDelMes(),
                'facturacion' => $facturacion,
                'evolucion_facturacion' => $this->evolucionFacturacion(),
                'num_transacciones' => $this->numeroTransacciones(),
                'ticket_promedio' => $this->ticketPromedio(),
            ],
            
            // Costos y utilidad
            'costos' => [
                'total_costo_venta' => $this->totalCostoVenta(),
                'porcentaje_costo_venta' => $this->calcularPorcentaje($this->totalCostoVenta(), $facturacion),
                'utilidad_bruta' => $utilidadBruta,
                'porcentaje_utilidad_bruta' => $this->calcularPorcentaje($utilidadBruta, $facturacion),
            ],
            
            // Gastos detallados
            'gastos' => [
                'personal' => [
                    'total' => $this->totalGastoPersonal(),
                    'porcentaje' => $this->calcularPorcentaje($this->totalGastoPersonal(), $facturacion)
                ],
                'operativos' => [
                    'total' => $this->totalGastosOperativos(),
                    'porcentaje' => $this->calcularPorcentaje($this->totalGastosOperativos(), $facturacion)
                ],
                'otros' => [
                    'total' => $this->totalOtrosGastos(),
                    'porcentaje' => $this->calcularPorcentaje($this->totalOtrosGastos(), $facturacion)
                ],
                'financieros_impuestos' => [
                    'total' => $this->totalFinancierosImpuestos(),
                    'porcentaje' => $this->calcularPorcentaje($this->totalFinancierosImpuestos(), $facturacion)
                ],
                'total_gastos' => $this->totalGastos(),
                'porcentaje_gastos' => $this->calcularPorcentaje($this->totalGastos(), $facturacion)
            ],
            
            // Resultados finales
            'resultados' => [
                'utilidad_neta' => $utilidadNeta,
                'porcentaje_utilidad_neta' => $this->calcularPorcentaje($utilidadNeta, $facturacion)
            ]
        ];
    }
}