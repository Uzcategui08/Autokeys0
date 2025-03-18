<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AlmaceneController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::resource('productos', ProductoController::class);

Route::resource('clientes', ClienteController::class);

Route::resource('inventarios', InventarioController::class);

Route::resource('almacenes', AlmaceneController::class);

Route::resource('presupuestos', PresupuestoController::class);

Route::get('/obtener-productos', [PresupuestoController::class, 'obtenerProductos']);

Route::get('/presupuesto/{id}/pdf', [PresupuestoController::class, 'generarPdf'])->name('presupuestos.pdf');

