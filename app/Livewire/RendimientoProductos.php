<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Producto;
use App\Models\DetalleTicket;

class RendimientoProductos extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    // Recibimos las fechas globales
    public $fechaInicio;
    public $fechaFin;

    // Sincronizamos el filtro con la URL
    #[Url]
    public $ranking_productos = 'ventas';

    public function updatingRankingProductos()
    {
        $this->resetPage('page_productos');
    }

   public function render()
    {
        $rangoFechas = [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59'];
        $estadosValidos = ['pagado', 'listo', 'entregado'];

        if ($this->ranking_productos === 'cero') {
            $queryProductos = Producto::with('categoria')
                ->whereDoesntHave('detalles', function($query) use ($rangoFechas, $estadosValidos) {
                    $query->whereHas('ticket', function($q) use ($rangoFechas, $estadosValidos) {
                        $q->whereIn('estado', $estadosValidos)->whereBetween('created_at', $rangoFechas);
                    });
                })->orderBy('nombre', 'asc');
        } else {
            $queryProductos = DetalleTicket::selectRaw('
                    producto_id,
                    SUM(cantidad) as total_vendido,
                    SUM(precio_unitario * cantidad) as ingreso_generado,
                    SUM((precio_unitario - precio_compra) * cantidad) as ganancia_generada
                ')
                ->whereHas('ticket', function($q) use ($rangoFechas, $estadosValidos) {
                    $q->whereIn('estado', $estadosValidos)->whereBetween('created_at', $rangoFechas);
                })
                ->with('producto.categoria')
                ->groupBy('producto_id');

            if ($this->ranking_productos === 'ganancia') {
                $queryProductos->orderByDesc('ganancia_generada');
            } else {
                $queryProductos->orderByDesc('total_vendido');
            }
        }

        $productosTop = $queryProductos->paginate(5)->onEachSide(1);

        return view('livewire.rendimiento-productos', compact('productosTop'));
    }
}