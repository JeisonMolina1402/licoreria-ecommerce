<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        // 2. DILE A LARAVEL QUE USE BOOTSTRAP 5 PARA LA PAGINACIÓN
        Paginator::useBootstrapFive();

        // Compartir notificaciones en las plantillas principales (Panel y Tienda)
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