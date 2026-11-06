<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DulceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Endpoint para verificar si la sesión sigue activa desde el navegador en segundo plano
    Route::get('/check-session', function () {
        return response()->json(['status' => 'active']);
    })->name('session.check');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Categorías de dulces
    Route::resource('categorias', CategoriaController::class);

    // Inventario de dulces
    Route::resource('dulces', DulceController::class);

    // Módulo de Ventas (Punto de Venta)
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/historial', [VentaController::class, 'historial'])->name('ventas.historial');

    // Módulo de Compras (Abastecimiento y cálculo de total)
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('/compras/historial', [CompraController::class, 'historial'])->name('compras.historial');

    // Gestión de usuarios
    Route::resource('usuarios', UsuarioController::class);

    // Configuración / Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
