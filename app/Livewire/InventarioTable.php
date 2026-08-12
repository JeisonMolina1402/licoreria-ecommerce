<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Producto;
use App\Models\Categoria;

class InventarioTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    #[Url]
    public $nombre = '';

    #[Url]
    public $categoria_id = '';

    #[Url]
    public $orden_stock = '';

    #[Url]
    public $orden_precio = '';

    public function updating($property)
    {
        $this->resetPage();
    }

   public function limpiar()
    {
        // En lugar de vaciar variables, recargamos la página limpia sin ningún filtro en la URL.
        return redirect()->route('inventario');
    }

    public function render()
    {
        $categorias = Categoria::all();
        $nombresProductos = Producto::select('nombre')->distinct()->pluck('nombre');

        $query = Producto::query();

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
            $query->latest();
        }

        $productos = $query->paginate(10);

        return view('livewire.inventario-table', [
            'productos' => $productos,
            'categorias' => $categorias,
            'nombresProductos' => $nombresProductos
        ]);
    }
}