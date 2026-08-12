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

        // 3. Paginación
        $tickets = $query->latest()->paginate(10);

        return view('livewire.tickets-table', [
            'tickets' => $tickets
        ]);
    }
}