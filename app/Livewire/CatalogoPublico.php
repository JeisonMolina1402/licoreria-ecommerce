<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Producto;
use App\Models\Categoria;

class CatalogoPublico extends Component
{
    use WithPagination;

    // Usar el paginador de Bootstrap en lugar del de Tailwind por defecto
    protected $paginationTheme = 'bootstrap';

    // Y asegúrate que la propiedad de búsqueda también lo tenga si quieres que guarde la palabra en la URL
    #[Url(history: true)]
    public $buscar = '';

    #[Url(as: 'categoria', history: true)]
    public $categoria_slug = '';


    // Si el usuario escribe en el buscador, regresamos a la página 1
    public function updatingBuscar()
    {
        $this->resetPage();
    }

    // Método para filtrar por categoría al hacer clic en el menú lateral
    public function setCategoria($slug)
    {
        $this->categoria_slug = $slug;
        $this->resetPage();
    }

    public function render()
    {
        $categorias = Categoria::all();

        // 1. Base: Solo productos con stock Y ACTIVOS
       $query = Producto::where('stock', '>', 0)->where('estado', 'activo');

        // 2. Filtro de búsqueda por nombre
        if (!empty($this->buscar)) {
            $query->where('nombre', 'LIKE', '%' . $this->buscar . '%');
        }

        // 3. Filtro por categoría (usando el slug para mantener las URLs bonitas)
        if (!empty($this->categoria_slug)) {
            $query->whereHas('categoria', function ($q) {
                $q->where('slug', $this->categoria_slug);
            });
        }

        // 4. Paginación
        $productos = $query->latest()->paginate(12);

        return view('livewire.catalogo-publico', compact('productos', 'categorias'));
    }
}