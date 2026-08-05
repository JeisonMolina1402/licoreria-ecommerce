<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\TurnoCajaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Historial de pedidos del cliente
Route::get('/mis-pedidos', [TiendaController::class, 'misPedidos'])
    ->middleware('auth')
    ->name('tienda.mis-pedidos');

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
    Route::get('/inventario/pdf', [InventarioController::class, 'exportarPdf'])->name('inventario.pdf')->middleware('auth');
    Route::post('/inventario/guardar', [InventarioController::class, 'store'])->name('inventario.store');
    Route::post('/inventario/actualizar/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/inventario/eliminar/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');

    // Módulo de Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::post('/reportes/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.pdf');

    // Módulo de Tickets (Ventas y Pedidos)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/nueva-venta', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/estado/{id}', [TicketController::class, 'cambiarEstado'])->name('tickets.estado');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    
    // Rutas para el Control de Caja
    Route::post('/caja/abrir', [TurnoCajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('/caja/cerrar', [TurnoCajaController::class, 'cerrar'])->name('caja.cerrar');

    // ==========================================
    // NUEVO: MÓDULO DE USUARIOS (ADMINISTRACIÓN)
    // ==========================================
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('usuarios.toggle');
});

// ==========================================
// RUTAS DE SEGURIDAD DE LARAVEL BREEZE
// ==========================================
require __DIR__ . '/auth.php';
