<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Gasto;
use App\Models\Costo;
use App\Models\Categoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $yearsRegistroV = RegistroV::selectRaw('EXTRACT(YEAR FROM fecha_h) as year')
            ->distinct()
            ->pluck('year');

        $yearsGastos = Gasto::selectRaw('EXTRACT(YEAR FROM f_gastos) as year')
            ->distinct()
            ->pluck('year');

        $yearsCostos = Costo::selectRaw('EXTRACT(YEAR FROM f_costos) as year')
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
    protected function totalCostosDelMes()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor');
    }
    protected function totalCostoVenta()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor');
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
        // Obtener subcategorías únicas (son strings, no IDs)
        $subcategorias = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->pluck('subcategoria')
            ->unique()
            ->filter(); // Elimina valores nulos

        $gastosPorSubcategoria = [];

        foreach ($subcategorias as $subcategoria) {
            $total = Gasto::whereYear('f_gastos', $this->year)
                ->whereMonth('f_gastos', $this->month)
                ->where('subcategoria', $subcategoria)
                ->sum('valor');

            $gastosPorSubcategoria[] = [
                'nombre' => $subcategoria, // Usamos directamente el nombre de la subcategoría
                'total' => $total,
                'porcentaje' => $this->calcularPorcentaje($total, $facturacion)
            ];
        }
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
                'total_costos_mes' => $this->totalCostosDelMes(),
                'porcentaje_total_costos' => $this->calcularPorcentaje($this->totalCostosDelMes(), $facturacion)
            ],
            'gastos' => [
                'por_subcategoria' => $gastosPorSubcategoria,
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
    //PDF   Que muestra todo
    public function showReportForm()
    {
        // Obtener fechas del mes actual
        $fechaInicio = Carbon::now()->firstOfMonth()->format('Y-m-d');
        $fechaFin = Carbon::now()->lastOfMonth()->format('Y-m-d');

        return view('registrosV.report-form', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
    }

    public function generatePdfTotal(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|numeric|min:2020|max:' . (date('Y') + 1)
        ]);

        $this->month = $request->input('month');
        $this->year = $request->input('year');

        // Obtener todas las estadísticas
        $stats = $this->getAllStats();

        // Obtener registros detallados
        $registros = RegistroV::with('empleado')
            ->whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->orderBy('fecha_h', 'desc')
            ->get();

        // Obtener gastos detallados
        $gastos = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->orderBy('f_gastos', 'desc')
            ->get();

        // Obtener costos detallados
        $costos = Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->orderBy('f_costos', 'desc')
            ->get();

        // Preparar datos para el PDF
        $data = [
            'title' => 'Reporte Estadístico Mensual',
            'date' => now()->format('d/m/Y H:i'),
            'mes' => Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
            'totalTrabajos' => $registros->sum(function ($r) {
                $items = $r->items;
                if (is_string($items)) {
                    $items = json_decode($items, true);
                } elseif (is_object($items)) {
                    $items = (array)$items;
                }
                return is_array($items) ? count($items) : 0;
            }),
            'totalPagos' => $registros->sum(function ($r) {
                $pagos = $r->pagos;
                if (is_string($pagos)) {
                    $pagos = json_decode($pagos, true);
                } elseif (is_object($pagos)) {
                    $pagos = (array)$pagos;
                }
                return is_array($pagos) ? array_sum(array_column($pagos, 'monto')) : 0;
            }),
        ];

        $pdf = PDF::loadView('estadisticas.stats-pdf', [
            'title' => 'Reporte Financiero',
            'mes' => $this->month . '/' . $this->year,
            'date' => now()->format('d/m/Y H:i'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
        ])->setPaper('a4', 'landscape'); // <-- Esto lo pone horizontal

        return $pdf->stream('reporte_financiero.pdf');
    }
}
