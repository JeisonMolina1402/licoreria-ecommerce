<?php

use App\Http\Controllers\AuditoriaController;
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


// ==========================================
// PROCESO DE COMPRA (CHECKOUT) - REQUIERE LOGIN DE CLIENTE Y VERIFICACIÓN
// ==========================================
Route::post('/checkout', [CheckoutController::class, 'procesar'])
    ->middleware(['auth', 'verified']) // <-- Añadido el 'verified'
    ->name('checkout.procesar');

Route::get('/checkout/exito/{id}', [CheckoutController::class, 'exito'])
    ->middleware(['auth', 'verified']) // <-- Añadido el 'verified'
    ->name('tienda.exito');

// Historial de pedidos del cliente
Route::get('/mis-pedidos', [TiendaController::class, 'misPedidos'])
    ->middleware(['auth', 'verified']) // <-- Añadido el 'verified'
    ->name('tienda.mis-pedidos');

// ==========================================
// PANEL ADMINISTRATIVO Y PERFIL - PROTEGIDO (SOLO ADMIN Y VENDEDOR)
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {

    //mas adelante verificar admins y vendedores con correors verificados reales
    //Route::middleware(['auth', 'verified', 'admin'])->group(function () {

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

    //Módulo de Auditoria
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

    // ==========================================
    // NUEVO: MÓDULO DE USUARIOS (ADMINISTRACIÓN)
    // ==========================================
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('usuarios.toggle');
});

// ==========================================
// NOTIFICACIONES GENERALES (ACCESO PARA TODOS LOS LOGEADOS)
// ==========================================
Route::post('/notificaciones/leer', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
})->middleware('auth')->name('notificaciones.leer');

// NUEVA RUTA: Consulta en tiempo real (AJAX Polling) sin recargar página
Route::get('/notificaciones/check', function () {
    if (!auth()->check()) return response()->json(['count' => 0]);
    
    $user = auth()->user();
    
    // Formateamos las últimas 5 notificaciones para que JS las entienda fácilmente
    $notificaciones = $user->notifications()->take(10)->get()->map(function($notif) {
        return [
            'id'      => $notif->id,
            'data'    => $notif->data, // Aquí vienen titulo, mensaje, icono, url
            'read_at' => $notif->read_at,
            'tiempo'  => $notif->created_at->diffForHumans() // Ej: "hace 2 minutos"
        ];
    });

    return response()->json([
        'count'          => $user->unreadNotifications()->count(),
        'notificaciones' => $notificaciones
    ]);
})->name('notificaciones.check');

// ==========================================
// RUTAS DE SEGURIDAD DE LARAVEL BREEZE
// ==========================================
require __DIR__ . '/auth.php';
