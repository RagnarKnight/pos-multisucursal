<?php
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Raíz: superadmin va al dashboard, los demás al POS
Route::get('/', function () {
    if (auth()->check() && auth()->user()->esSuperAdmin()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('pos.index');
});

Route::middleware(['auth'])->group(function () {

    // ── Dashboard superadmin ──────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:superadmin');

    // ── POS ───────────────────────────────────────────────────────
    Route::get('/pos',        [OrderController::class, 'create'])->name('pos.index');
    Route::post('/pos/venta', [OrderController::class, 'store'])->name('pos.store');

    // ── Historial ─────────────────────────────────────────────────
    Route::get('/historial',           [OrderController::class, 'index'])->name('orders.index');
    Route::get('/historial/{order}',   [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/historial/{order}', [OrderController::class, 'update'])->name('orders.update');

    // ── Clientes (La Libreta) ─────────────────────────────────────
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/saldar', [CustomerController::class, 'saldar'])
         ->name('customers.saldar');

    // ── Reportes ──────────────────────────────────────────────────
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');

    // ── Control de Caja ───────────────────────────────────────────
    Route::get('/caja',                [CajaController::class, 'index'])->name('cajas.index');
    Route::post('/caja/abrir',         [CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::post('/caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');

    // ── Switch de tienda activa (solo superadmin) ─────────────────
    Route::post('/tienda/switch', [TiendaController::class, 'switchTienda'])
         ->name('tienda.switch')
         ->middleware('can:superadmin');

    // ── Configuración de tienda (admin + superadmin) ──────────────
    Route::middleware('can:configurar-tienda')->group(function () {
        Route::get('/tiendas/{tienda}/edit', [TiendaController::class, 'edit'])
             ->name('tiendas.edit');
        Route::put('/tiendas/{tienda}', [TiendaController::class, 'update'])
             ->name('tiendas.update');
    });

    // ── CRUD completo de tiendas (superadmin + flag en .env) ──────
    // Activar con: ALLOW_TIENDA_MANAGEMENT=true
    Route::middleware('can:gestionar-tiendas')->group(function () {
        Route::get('/tiendas',             [TiendaController::class, 'index'])->name('tiendas.index');
        Route::get('/tiendas/crear',       [TiendaController::class, 'create'])->name('tiendas.create');
        Route::post('/tiendas',            [TiendaController::class, 'store'])->name('tiendas.store');
        Route::delete('/tiendas/{tienda}', [TiendaController::class, 'destroy'])->name('tiendas.destroy');
    });

    // ── Admin: usuarios y productos ───────────────────────────────
    Route::middleware('can:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('/products/{product}', [ProductController::class, 'update'])
             ->name('products.update.precio');
        Route::resource('products', ProductController::class)->except(['update']);
        Route::put('/products/{product}', [ProductController::class, 'update'])
             ->name('products.update');
    });

});

require __DIR__ . '/auth.php';
