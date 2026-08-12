<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    /**
     * Muestra el catálogo principal de la tienda al cliente.
     */
    public function index()
    {
        // Solo necesitamos las categorías para el menú móvil que está en el layout principal
        $categorias = Categoria::all();
        return view('tienda.index', compact('categorias'));
    }

    /**
     * Muestra el historial de compras del cliente logueado.
     */
    public function misPedidos()
    {

        $tickets = Ticket::with('detalles.producto')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tienda.mis-pedidos', compact('tickets'));
    }
}
