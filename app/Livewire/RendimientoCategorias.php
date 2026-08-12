<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\DetalleTicket;
use App\Models\Categoria;

class RendimientoCategorias extends Component
{
    public $fechaInicio;
    public $fechaFin;
    public $tieneTickets;

    #[Url]
    public $ranking_categorias = 'ventas';

    public function render()
    {
        $rangoFechas = [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59'];

        $detallesParaCategorias = DetalleTicket::whereHas('ticket', function($query) use ($rangoFechas) {
            $query->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
        })->with('producto.categoria')->get();

        $ventasPorCategoria = [];
        $todasLasCategorias = Categoria::pluck('nombre');

        foreach ($todasLasCategorias as $nombreCategoria) {
            $ventasPorCategoria[$nombreCategoria] = [
                'unidades' => 0, 'inversion' => 0, 'ventas' => 0, 'ganancia' => 0
            ];
        }
        $ventasPorCategoria['Sin Categoría'] = ['unidades' => 0, 'inversion' => 0, 'ventas' => 0, 'ganancia' => 0];

        foreach ($detallesParaCategorias as $detalle) {
            $catNombre = $detalle->producto->categoria->nombre ?? 'Sin Categoría';
            $inversion = $detalle->precio_compra * $detalle->cantidad;
            $venta = $detalle->precio_unitario * $detalle->cantidad;

            $ventasPorCategoria[$catNombre]['unidades'] += $detalle->cantidad;
            $ventasPorCategoria[$catNombre]['inversion'] += $inversion;
            $ventasPorCategoria[$catNombre]['ventas'] += $venta;
            $ventasPorCategoria[$catNombre]['ganancia'] += ($venta - $inversion);
        }

        if ($this->ranking_categorias === 'ganancia') {
            $ventasPorCategoria = collect($ventasPorCategoria)->sortByDesc('ganancia');
        } elseif ($this->ranking_categorias === 'cero') {
            $ventasPorCategoria = collect($ventasPorCategoria)->where('unidades', 0);
        } else {
            $ventasPorCategoria = collect($ventasPorCategoria)->sortByDesc('unidades');
        }

        $nombresCategorias = $ventasPorCategoria->keys()->toJson();
        $cantidadesCategorias = $ventasPorCategoria->pluck('unidades')->toJson();

        // Magia: Avisamos a JavaScript que los datos cambiaron para animar la dona
        $this->dispatch('actualizarGraficoDona', [
            'labels' => json_decode($nombresCategorias), 
            'valores' => json_decode($cantidadesCategorias)
        ]);

        return view('livewire.rendimiento-categorias', compact('ventasPorCategoria', 'nombresCategorias', 'cantidadesCategorias'));
    }
}