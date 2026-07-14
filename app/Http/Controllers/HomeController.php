<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Ticket;
use Carbon\Carbon; // Necesario para manejar las fechas de hoy

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Ventas Diarias: Sumamos el total de tickets creados HOY que estén pagados o entregados
        $ventasDiarias = Ticket::whereDate('created_at', Carbon::today())
                               ->whereIn('estado', ['pagado', 'entregado'])
                               ->sum('total');

        // 2. Tickets Pendientes: Contamos cuántos tickets tienen el estado 'pendiente'
        $ticketsPendientes = Ticket::where('estado', 'pendiente')->count();

        // 3. Low Stock: Contamos cuántos productos tienen 10 o menos de stock
        $lowStock = Producto::where('stock', '<=', 10)->count();

        // 4. Total de Productos: Contamos todo el catálogo
        $totalProductos = Producto::count();

        // 5. Últimos 5 productos para la tabla inferior
        $ultimosProductos = Producto::with('categoria')->latest()->take(5)->get();

        return view('home', compact(
            'ventasDiarias', 
            'ticketsPendientes', 
            'lowStock', 
            'totalProductos', 
            'ultimosProductos'
        ));
    }
}