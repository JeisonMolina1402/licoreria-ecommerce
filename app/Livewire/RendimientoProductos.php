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

        $queryProductos = Producto::with('categoria')
            ->select('productos.*') 
            ->withSum(['detalles as total_vendido' => function($query) use ($rangoFechas) {
                $query->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                });
            }], 'cantidad')
            ->addSelect(['ingreso_generado' => DetalleTicket::selectRaw('SUM(detalle_tickets.precio_unitario * detalle_tickets.cantidad)')
                ->whereColumn('detalle_tickets.producto_id', 'productos.id')
                ->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                })
            ])
            ->addSelect(['ganancia_generada' => DetalleTicket::selectRaw('SUM((detalle_tickets.precio_unitario - detalle_tickets.precio_compra) * detalle_tickets.cantidad)')
                ->whereColumn('detalle_tickets.producto_id', 'productos.id')
                ->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                })
            ]);

        if ($this->ranking_productos === 'cero') {
            $queryProductos->whereDoesntHave('detalles', function($query) use ($rangoFechas) {
                $query->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                });
            })->orderBy('nombre', 'asc');
        } elseif ($this->ranking_productos === 'ganancia') {
            $queryProductos->whereHas('detalles', function($query) use ($rangoFechas) {
                $query->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                });
            })->orderByRaw('COALESCE(ganancia_generada, 0) DESC');
        } else {
            $queryProductos->whereHas('detalles', function($query) use ($rangoFechas) {
                $query->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
                });
            })->orderByRaw('COALESCE(total_vendido, 0) DESC');
        }

        $productosTop = $queryProductos->paginate(5, ['*'], 'page_productos');

        return view('livewire.rendimiento-productos', compact('productosTop'));
    }
}