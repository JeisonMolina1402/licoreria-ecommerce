<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class TiendaController extends Controller
{
    /**
     * Muestra el catálogo principal de la tienda al cliente.
     */
    public function index(Request $request, Categoria $categoria = null)
    {
        // 1. Traemos las categorías para ponerlas en un menú de filtros
        $categorias = Categoria::all();

        // 2. Traemos SOLO los productos que tengan stock mayor a 0 (para no vender lo que no hay)
        $query = Producto::where('stock', '>', 0);

        // Si el cliente usa la barra de búsqueda
        if ($request->filled('buscar')) {
            $query->where('nombre', 'LIKE', '%' . $request->buscar . '%');
        }

        // SI EXISTE $categoria (porque entramos por /categoria/whisky), filtramos por su ID
    if ($categoria) {
        $query->where('categoria_id', $categoria->id);
    }

        // 3. Paginamos los productos de 12 en 12 (formato cuadrícula para tienda)
        $productos = $query->latest()->paginate(12)->appends($request->all());

        return view('tienda.index', compact('productos', 'categorias'));
    }

    /**
     * Muestra el historial de compras del cliente logueado.
     */
    public function misPedidos()
    {
        // Buscamos los tickets que pertenezcan al ID del usuario actual, ordenados del más reciente al más antiguo
        $tickets = \App\Models\Ticket::with('detalles.producto')
                    ->where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('tienda.mis-pedidos', compact('tickets'));
    }
}