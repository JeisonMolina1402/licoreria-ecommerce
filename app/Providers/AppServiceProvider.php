<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate; // <--- NUEVO: Importamos la clase Gate

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // =========================================================
        // 1. GUARDIA DE SEGURIDAD SUPREMO (SPATIE)
        // Le da acceso total a quien tenga el Rol 'ADMIN' sin importar su nombre
        // =========================================================
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // 2. DILE A LARAVEL QUE USE BOOTSTRAP 5 PARA LA PAGINACIÓN
        Paginator::useBootstrapFive();

        // 3. Compartir notificaciones en las plantillas principales (Panel y Tienda)
        View::composer(['layouts.app', 'layouts.tienda', 'layouts.navigation'], function ($view) {
            if (Auth::check()) {
                $view->with([
                    'notifications' => Auth::user()->notifications()->take(10)->get(),
                    'unreadCount'   => Auth::user()->unreadNotifications()->count(),
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadCount'   => 0,
                ]);
            }
        });
    }
}