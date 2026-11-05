<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DulceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

    // Gestión de usuarios
    Route::resource('usuarios', UsuarioController::class);

    // Configuración / Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';