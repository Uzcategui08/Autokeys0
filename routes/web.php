<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AlmaceneController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroVController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, "index"])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::resource('productos', ProductoController::class);

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

