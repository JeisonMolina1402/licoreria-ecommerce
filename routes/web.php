<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController; // <-- Controlador del perfil

// ==========================================
// TIENDA PÚBLICA (E-COMMERCE) - ACCESO LIBRE
// ==========================================
Route::get('/', [TiendaController::class, 'index'])->name('tienda.index');

// ruta amigable para categorías
Route::get('/categoria/{categoria}', [TiendaController::class, 'index'])->name('tienda.categoria');

// ==========================================
// PROCESO DE COMPRA (CHECKOUT) - REQUIERE LOGIN DE CLIENTE
// ==========================================
Route::post('/checkout', [CheckoutController::class, 'procesar'])
    ->middleware('auth')
    ->name('checkout.procesar');

Route::get('/checkout/exito/{id}', [CheckoutController::class, 'exito'])
    ->middleware('auth')
    ->name('tienda.exito');

// ==========================================
// PANEL ADMINISTRATIVO Y PERFIL - PROTEGIDO (SOLO ADMIN Y VENDEDOR)
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    
    // Rutas del Perfil de Usuario 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Breeze usa 'dashboard' por defecto, redirigimos a tu HomeController
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Módulo de Inventario
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::post('/inventario/guardar', [InventarioController::class, 'store'])->name('inventario.store');
    Route::post('/inventario/actualizar/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/inventario/eliminar/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');

    // Módulo de Tickets (Ventas y Pedidos)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/nueva-venta', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/estado/{id}', [TicketController::class, 'cambiarEstado'])->name('tickets.estado');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
});

// ==========================================
// RUTAS DE SEGURIDAD DE LARAVEL BREEZE
// ==========================================
require __DIR__.'/auth.php';