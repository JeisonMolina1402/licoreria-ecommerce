<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\RolPermisoController;
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
    ->middleware(['auth', 'verified'])
    ->name('checkout.procesar');

Route::get('/checkout/exito/{id}', [CheckoutController::class, 'exito'])
    ->middleware(['auth', 'verified'])
    ->name('tienda.exito');

// Historial de pedidos del cliente
Route::get('/mis-pedidos', [TiendaController::class, 'misPedidos'])
    ->middleware(['auth', 'verified'])
    ->name('tienda.mis-pedidos');

// ==========================================
// PANEL ADMINISTRATIVO Y PERFIL - PROTEGIDO CON SPATIE
// ==========================================
// SPATIE: Solo usuarios con el rol 'admin' O 'vendedor' pueden entrar a este grupo general
Route::middleware(['auth', 'role:admin|vendedor'])->group(function () {

    // Rutas del Perfil de Usuario (Accesible para admins y vendedores)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard Base (Accesible para admins y vendedores, protegido por permiso)
    Route::middleware(['permission:ver dashboard'])->group(function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/home', [HomeController::class, 'index'])->name('home');
    });

    // Módulo de Inventario
    Route::middleware(['permission:gestionar inventario'])->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::get('/inventario/pdf', [InventarioController::class, 'exportarPdf'])->name('inventario.pdf');
        Route::post('/inventario/guardar', [InventarioController::class, 'store'])->name('inventario.store');
        Route::post('/inventario/actualizar/{id}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::delete('/inventario/eliminar/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
    });

    // Módulo de Tickets (Ventas y Pedidos)
    Route::middleware(['permission:gestionar tickets'])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/nueva-venta', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets/estado/{id}', [TicketController::class, 'cambiarEstado'])->name('tickets.estado');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');


        // Rutas para el Control de Caja
        Route::post('/caja/abrir', [TurnoCajaController::class, 'abrir'])->name('caja.abrir');
        Route::post('/caja/cerrar', [TurnoCajaController::class, 'cerrar'])->name('caja.cerrar');

        // Rutas para el Subir Comprobante
        Route::post('/tickets/{id}/comprobante', [App\Http\Controllers\TicketController::class, 'subirComprobante'])->name('tickets.comprobante');
    });

    // Módulo de Reportes
    Route::middleware(['permission:gestionar reportes'])->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::post('/reportes/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.pdf');
    });

    // Módulo de Auditoria
    Route::middleware(['permission:ver auditoria'])->group(function () {
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    });

    // Exportar PDF de Auditoria
    Route::get('/auditoria/pdf', [App\Http\Controllers\AuditoriaController::class, 'exportarPdf'])->name('auditoria.pdf');

    // Módulo de Usuarios
    Route::middleware(['permission:gestionar usuarios'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/estado', [UserController::class, 'toggleEstado'])->name('usuarios.toggle');
    });

    // ==========================================
    // MÓDULO DE SEGURIDAD (ROLES Y PERMISOS)
    // ==========================================
    // Ojo: En un sistema real, gestionar permisos es solo para el Super Admin
    Route::middleware(['permission:gestionar roles y permisos'])->group(function () {

        // Rutas para Roles
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RolController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RolController::class, 'destroy'])->name('roles.destroy');

        // Rutas para asignar permisos a un Rol específico (Lo que vimos en clase)
        Route::get('/roles/{id}/permisos', [RolPermisoController::class, 'index'])->name('roles.permisos');
        Route::post('/roles/{role}/permisos', [RolPermisoController::class, 'updatePermissions'])->name('roles.permisos.update');

        // Rutas para Permisos del sistema
        Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
        Route::post('/permisos', [PermisoController::class, 'store'])->name('permisos.store');
        Route::put('/permisos/{permission}', [PermisoController::class, 'update'])->name('permisos.update');
        Route::delete('/permisos/{permission}', [PermisoController::class, 'destroy'])->name('permisos.destroy');
    });
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

    // Formateamos las últimas 10 notificaciones para que JS las entienda fácilmente
    $notificaciones = $user->notifications()->take(10)->get()->map(function ($notif) {
        return [
            'id'      => $notif->id,
            'data'    => $notif->data,
            'read_at' => $notif->read_at,
            'tiempo'  => $notif->created_at->diffForHumans()
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
