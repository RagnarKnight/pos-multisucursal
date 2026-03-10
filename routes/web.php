<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('pos.index'));

Route::middleware(['auth'])->group(function () {

    // ── POS ───────────────────────────────────────────────────────
    Route::get('/pos',        [OrderController::class, 'create'])->name('pos.index');
    Route::post('/pos/venta', [OrderController::class, 'store'])->name('pos.store');

    // ── Historial / Cierre de caja ────────────────────────────────
    Route::get('/historial',          [OrderController::class, 'index'])->name('orders.index');
    Route::get('/historial/{order}',    [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/historial/{order}',  [OrderController::class, 'update'])->name('orders.update');

    // ── Clientes (La Libreta) ─────────────────────────────────────
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/saldar', [CustomerController::class, 'saldar'])
         ->name('customers.saldar');

    // ── Reportes ──────────────────────────────────────────────────
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');

    // ── Control de Caja ───────────────────────────────────────────
    Route::get('/caja',                      [CajaController::class, 'index'])->name('cajas.index');
    Route::post('/caja/abrir',               [CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::post('/caja/{caja}/cerrar',       [CajaController::class, 'cerrar'])->name('cajas.cerrar');

    // ── Usuarios (solo admin) ─────────────────────────────────────
    Route::middleware('can:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // ── Productos (solo admin) ────────────────────────────────────
    Route::middleware('can:admin')->group(function () {
        Route::patch('/products/{product}', [ProductController::class, 'update'])
             ->name('products.update.precio');
        Route::resource('products', ProductController::class)->except(['update']);
        Route::put('/products/{product}', [ProductController::class, 'update'])
             ->name('products.update');
    });

});

require __DIR__ . '/auth.php';
