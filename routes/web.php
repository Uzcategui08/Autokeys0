<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AlmaceneController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroVController;
use App\Http\Controllers\NempleadoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\CostoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TiposDePagoController;
use App\Http\Controllers\EstadisticasVentasController;
use App\Http\Controllers\CierreVentasController;
use App\Http\Controllers\VanesController;
use App\Http\Controllers\CierreVentasSemanalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\TrabajoController;
use App\Models\Inventario;
use App\Http\Controllers\CategoriaController;
use Illuminate\Http\Request;
use App\Models\Trabajo;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', [DashboardController::class, "index"])->middleware(['auth', 'verified'])->name('dashboard');



// Permisos (opcional, si quieres gestionarlos por separado)
//Route::resource('permissions', PermissionController::class)->middleware(['auth', 'role:admin']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

//Route::resource('ajustar-inventarios', AjustarInventarioController::class);

Route::resource('clientes', ClienteController::class);

Route::resource('productos', ProductoController::class);

route::get('admin/dashboard', [DashboardController::class, "index"])->name('admin.dashboard');

Route::resource('transferencias', TransferenciaController::class);

Route::get('/inventarios/data', [InventarioController::class, 'getData'])->name('inventarios.data');

Route::get('/inventarios/export', [InventarioController::class, 'export'])->name('inventarios.export');
Route::resource('inventarios', InventarioController::class);
Route::get('/cargas', [InventarioController::class, 'cargas'])->name('inventario.cargas');
// Rutas para el sistema de ajustes
Route::get('/inventarios/{inventario}/ajustar', [InventarioController::class, 'editarConAjustes'])
    ->name('inventarios.ajustar');
Route::post('/inventarios/{inventario}/ajustar', [InventarioController::class, 'actualizarConAjustes'])
    ->name('inventarios.actualizar-ajustes');

Route::resource('almacenes', AlmaceneController::class);

Route::resource('presupuestos', PresupuestoController::class)->middleware('auth');

// Reporte de Cuentas por Cobrar
Route::get('/reportes/cxc', [RegistroVController::class, 'reporteCxc'])->name('reportes.cxc');
Route::get('/reportes/cxc/detalle-cliente', [RegistroVController::class, 'detalleCliente'])->name('reportes.cxc.detalle-cliente');
Route::get('/reportes/cxc/generar-pdf', [RegistroVController::class, 'cxcPdf'])->name('reportes.cxc.generar-pdf');

Route::patch('/registro-vs/{registroV}/toggle-cargado', [RegistroVController::class, 'toggleCargado'])
    ->name('registro-vs.toggle-cargado');

Route::resource('registro-vs', RegistroVController::class);

Route::get('/estadisticas-ventas/{month?}/{year?}', [EstadisticasVentasController::class, 'index'])
    ->name('estadisticas.ventas');

Route::get('/cierre-ventas', [CierreVentasController::class, 'index'])->name('cierre.mensual');
Route::get('/cierre-ventas', [CierreVentasController::class, 'index'])->name('cierre.ventas');
Route::get('/estadisticas/RegistroVpdf', [EstadisticasVentasController::class, 'generatePdfTotal'])->name('generatePdfTotal.pdf');

Route::get('/cierre-ventas', [CierreVentasController::class, 'index'])->name('cierre.mensual');
Route::get('/cierre-ventas-semanal', [CierreVentasSemanalController::class, 'index'])->name('cierre.semanal');

// Rutas de exportación para cierre semanal
Route::get('/cierre-ventas-semanal/export-pdf', [CierreVentasSemanalController::class, 'exportPdf'])->name('cierre-ventas-semanal.export-pdf');
Route::get('/cierre-ventas-semanal/export-excel', [CierreVentasSemanalController::class, 'exportExcel'])->name('cierre-ventas-semanal.export-excel');
Route::get('/cierre-ventas-semanal/export-excel', [CierreVentasSemanalController::class, 'exportExcel'])->name('cierre-ventas-semanal.export-excel');

Route::get('/estadisticas-vanes', [VanesController::class, 'index'])->name('ventas.reporte');
Route::get('/estadisticas/vanespdf', [VanesController::class, 'descargarReporteFinDeSemana'])->name('ventas.descargar-reporte');
Route::get('/estadisticas/vanes/export-excel', [VanesController::class, 'exportExcel'])->name('vanes.exportExcel');
Route::get('/estadisticas/vanes/export-pdf', [VanesController::class, 'exportPdf'])->name('vanes.exportPdf');

Route::resource('tipos-de-pagos', TiposDePagoController::class);

Route::get('/cxc', [RegistroVController::class, 'cxc'])->name('registroV.cxc');

Route::resource('almacenes', AlmaceneController::class);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/obtener-productos-registroV', [RegistroVController::class, 'obtenerProductosV']);

Route::get('/obtener-productos', [PresupuestoController::class, 'obtenerProductos']);
Route::get('/presupuesto/{id}/pdf', [PresupuestoController::class, 'generarPdf'])->name('presupuestos.pdf');
Route::get('/budget/{id}/pdf', [PresupuestoController::class, 'generatePdf'])->name('budget.pdf');

Route::get('/registro-vs/pdf/{id}', [RegistroVController::class, 'generarPdf'])->name('registro-vs.pdf');
Route::get('/invoice/pdf/{id}', [RegistroVController::class, 'generatePdf'])->name('invoice.pdf');


Route::resource('ordens', OrdenController::class);
Route::get('/obtener-productos-orden', [OrdenController::class, 'obtenerProductos']);
Route::get('/orden/{id}/pdf', [OrdenController::class, 'generarPdf'])->name('ordens.pdf');

Route::resource('empleados', EmpleadoController::class);


Route::prefix('nempleados')->group(function () {
    Route::get('/', [NempleadoController::class, 'index'])->name('nempleados.index');
    Route::get('/create', [NempleadoController::class, 'create'])->name('nempleados.create');
    Route::post('/', [NempleadoController::class, 'store'])->name('nempleados.store');
    Route::get('/{id}/edit', [NempleadoController::class, 'edit'])->name('nempleados.edit');
    Route::put('/{id}', [NempleadoController::class, 'update'])->name('nempleados.update');
    Route::delete('/{id}', [NempleadoController::class, 'destroy'])->name('nempleados.destroy');
    Route::get('/generar-reporte', [NempleadoController::class, 'reporte'])
        ->name('nempleados.reporte');

    Route::get('/detalle/{id}', [NempleadoController::class, 'show'])
        ->name('nempleados.show');
});
Route::get('/nomina/registros', [NempleadoController::class, 'getRegistros'])->name('nomina.getRegistros');

// PDF Individual
Route::get('/nempleados/pdf/{id}', [NempleadoController::class, 'generarReciboIndividual'])->name('nempleados.pdf');

// PDF General
Route::get('/nomina/generar-recibo-general/{fechaDesde}/{fechaHasta}', [NempleadoController::class, 'generarReciboGeneral'])
    ->name('nempleados.general')
    ->where([
        'fechaDesde' => '^[0-9]{4}-[0-9]{2}-[0-9]{2}$',
        'fechaHasta' => '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
    ]);

Route::get('/nempleados/reporte', [NempleadoController::class, 'reporte'])->name('nempleados.reporte');

Route::get('/verificar-stock', [RegistroVController::class, 'verificarStock'])->name('verificar.stock');

Route::resource('prestamos', PrestamoController::class);
Route::get('prestamos/empleado/{id}', [PrestamoController::class, 'porEmpleado'])
    ->name('prestamos.empleado');
Route::get('prestamos/{id}/cuotas', [PrestamoController::class, 'showCuotas'])
    ->name('prestamos.cuotas');


Route::resource('abonos', AbonoController::class);

Route::resource('descuentos', DescuentoController::class);

Route::resource('costos', CostoController::class);
Route::resource('gastos', GastoController::class);

Route::delete('/registro-vs/{id}/costos/{costo_id}', [RegistroVController::class, 'eliminarCosto'])->name('registro-vs.eliminar-costo');
Route::delete('/registro-vs/{id}/gastos/{gasto_id}', [RegistroVController::class, 'eliminarGasto'])->name('registro-vs.eliminar-gasto');

Route::get('/inventario/{productoId}/{almacenId}', function ($productoId, $almacenId) {
    $inventario = Inventario::where('id_producto', $productoId)
        ->where('id_almacen', $almacenId)
        ->first();

    if (!$inventario) {
        return response()->json([
            'cantidad' => 0
        ]);
    }

    return response()->json([
        'cantidad' => $inventario->cantidad
    ]);
});

Route::post('/estadisticas/pdf', [EstadisticasVentasController::class, 'generateStatsPdf'])
    ->name('estadisticas.pdf')
    ->middleware('auth');

Route::get('/verificar-stock-transferencia', [TransferenciaController::class, 'verificarStock'])->name('verificarTransferencia.stock');

Route::get('/empleados/{id}/datos-pago', [EmpleadoController::class, 'getDatosPago'])->name('empleados.datos-pago');
Route::get('/empleados/{id}/datos-pago', [EmpleadoController::class, 'getDatosPago']);

Route::get('/productos-por-almacen/{almacenId}', [InventarioController::class, 'productosPorAlmacen']);
Route::get('/verificar-stock/{productoId}', [InventarioController::class, 'verificarStock']);

Route::resource('trabajos', TrabajoController::class);

Route::get('/obtener-trabajos', [TrabajoController::class, 'obtenerTrabajos'])->name('obtener.trabajos');

Route::get('/obtener-todos-trabajos', [RegistroVController::class, 'obtenerTodosLosTrabajos']);

Route::resource('categorias', CategoriaController::class);

// Eliminar un ajuste de inventario (carga/descarga)
Route::delete('/ajustes/{id}', [InventarioController::class, 'destroyAjuste'])->name('ajustes.destroy');
