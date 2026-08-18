<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Producto;
use App\Models\Categoria;

class PosTable extends Component
{
    #[Url]
    public $nombre = '';

    #[Url]
    public $categoria_id = '';

    #[Url]
    public $orden_stock = '';

    #[Url]
    public $orden_precio = '';

    public function limpiar()
    {
        return redirect()->route('tickets.create');
    }

    public function render()
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        $query = Producto::where('stock', '>', 0)->where('estado', 'activo');

        if (!empty($this->nombre)) {
            $query->where('nombre', 'LIKE', '%' . $this->nombre . '%');
        }
        if (!empty($this->categoria_id)) {
            $query->where('categoria_id', $this->categoria_id);
        }
        if (!empty($this->orden_stock)) {
            $query->orderBy('stock', $this->orden_stock);
        }
        if (!empty($this->orden_precio)) {
            $query->orderBy('precio', $this->orden_precio);
        }

        if (empty($this->orden_stock) && empty($this->orden_precio)) {
            $query->orderBy('nombre', 'asc');
        }

        // En el POS normalmente no se usa paginación para poder ver todos los productos rápidamente, usamos get()
        $productos = $query->get();

        return view('livewire.pos-table', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }
}