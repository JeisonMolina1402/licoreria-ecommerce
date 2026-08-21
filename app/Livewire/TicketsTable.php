<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Ticket;

class TicketsTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    #[Url]
    public $buscar_codigo = '';

    #[Url]
    public $estado = '';

    #[Url]
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    // 🔥 NUEVO: Método mount para inicializar todo automáticamente en el día actual
    public function mount()
    {
        // Si no hay fechas en la URL, ponemos las de hoy por defecto
        if (empty($this->fecha_inicio)) {
            $this->fecha_inicio = date('Y-m-d');
        }
        if (empty($this->fecha_fin)) {
            $this->fecha_fin = date('Y-m-d');
        }
    }

    // Resetear paginación al escribir
    public function updating($property)
    {
        $this->resetPage();
    }

    // Botón limpiar redirige para borrar la URL
    public function limpiar()
    {
        return redirect()->route('tickets.index');
    }

    public function render()
    {
        // 1. Consulta optimizada
        $query = Ticket::with(['user', 'detalles.producto']);

        // 2. Filtros dinámicos en tiempo real
        if (!empty($this->buscar_codigo)) {
            $query->where('codigo_reserva', 'LIKE', '%' . $this->buscar_codigo . '%');
        }

        if (!empty($this->estado)) {
            $query->where('estado', $this->estado);
        }

        // 3. Filtros de Fechas automáticos
        if (!empty($this->fecha_inicio)) {
            $query->whereDate('created_at', '>=', $this->fecha_inicio);
        }

        if (!empty($this->fecha_fin)) {
            $query->whereDate('created_at', '<=', $this->fecha_fin);
        }

        // 4. Paginación
        $tickets = $query->latest()->paginate(10)->onEachSide(1);

        return view('livewire.tickets-table', [
            'tickets' => $tickets
        ]);
    }
}