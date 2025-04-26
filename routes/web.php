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
use App\Http\Controllers\TnominaController;
use App\Http\Controllers\PnominaController;
use App\Http\Controllers\NempleadoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\CostoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AjustarInventarioController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, "index"])->middleware(['auth', 'verified'])->name('dashboard');


// Permisos (opcional, si quieres gestionarlos por separado)
Route::resource('permissions', PermissionController::class)->middleware(['auth', 'role:admin']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::resource('ajustar-inventarios', AjustarInventarioController::class);

Route::resource('clientes', ClienteController::class);

route::get('admin/dashboard', [DashboardController::class, "index"])->name('admin.dashboard');

Route::resource('inventarios', InventarioController::class);

Route::resource('almacenes', AlmaceneController::class);

Route::resource('presupuestos', PresupuestoController::class);

Route::resource('registro-vs', RegistroVController::class);

Route::get('/cxc', [RegistroVController::class, 'cxc'])->name('registroV.cxc');

Route::resource('almacenes', AlmaceneController::class);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/obtener-productos-registroV', [RegistroVController::class, 'obtenerProductosV']);

Route::get('/obtener-productos', [PresupuestoController::class, 'obtenerProductos']);
Route::get('/presupuesto/{id}/pdf', [PresupuestoController::class, 'generarPdf'])->name('presupuestos.pdf');

Route::resource('ordens', OrdenController::class);
Route::get('/obtener-productos-orden', [OrdenController::class, 'obtenerProductos']);
Route::get('/orden/{id}/pdf', [OrdenController::class, 'generarPdf'])->name('ordens.pdf');

Route::resource('empleados', EmpleadoController::class);

Route::resource('tnominas', TnominaController::class);

Route::resource('pnominas', PnominaController::class);

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
    

    Route::get('/recibo/{periodoId}/{empleadoId}', [NempleadoController::class, 'generarRecibo'])
        ->name('nempleados.pdf');
    Route::get('/general/{periodoId}', [NempleadoController::class, 'generarReciboGeneral'])
        ->name('nempleados.general');

});
Route::get('/empleados-por-tnomina/{id_tnomina}', function($id_tnomina) {
    $empleados = App\Models\Empleado::where('id_tnomina', $id_tnomina)->get();
    return response()->json(['empleados' => $empleados]);
});


Route::resource('prestamos', PrestamoController::class);
Route::get('prestamos/empleado/{id}', [PrestamoController::class, 'porEmpleado'])
    ->name('prestamos.empleado');
Route::get('prestamos/{id}/cuotas', [PrestamoController::class, 'showCuotas'])
->name('prestamos.cuotas');

Route::resource('cuotas', CuotaController::class);

Route::resource('abonos', AbonoController::class);

Route::resource('descuentos', DescuentoController::class);

Route::resource('costos', CostoController::class);

Route::resource('gastos', GastoController::class);



