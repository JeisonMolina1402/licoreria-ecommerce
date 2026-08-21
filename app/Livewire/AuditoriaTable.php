<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class AuditoriaTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $modulo = '';
    public $filtroAccion = ''; 
    public $filtroRol = '';
    public $searchUsuario = '';
    public $fechaInicio;
    public $fechaFin;

    // 🔥 1. CREAMOS LA LLAVE MÁGICA
    public $resetKey = 0; 

    public function mount()
    {
        $this->fechaInicio = date('Y-m-d');
        $this->fechaFin = date('Y-m-d');
    }

    public function updating($property)
    {
        $this->resetPage();
    }

    // Método que llamará nuestro botón "Limpiar"
    public function limpiarFiltros()
    {
        // Al estilo Inventario: Infalible y directo.
        return redirect()->route('auditoria.index');
    }

    public function render()
    {
        $query = Activity::with('causer')->latest();

        if ($this->modulo !== '') {
            $query->where('log_name', $this->modulo);
        }

        // 🔥 3. NUEVA CONDICIÓN PARA FILTRAR POR LA ACCIÓN EXACTA
        if ($this->filtroAccion !== '') {
            $query->where('event', $this->filtroAccion);
        }

        if ($this->fechaInicio) {
            $query->whereDate('created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('created_at', '<=', $this->fechaFin);
        }

        if ($this->filtroRol !== '' || $this->searchUsuario !== '') {
            $query->whereHasMorph('causer', [User::class], function ($q) {
                if ($this->filtroRol !== '') {
                    $q->where('rol', $this->filtroRol);
                }
                if ($this->searchUsuario !== '') {
                    $q->where(function($subQ) {
                        $subQ->where('name', 'like', '%' . $this->searchUsuario . '%')
                             ->orWhere('cedula', 'like', '%' . $this->searchUsuario . '%');
                    });
                }
            });
        }

        $logs = $query->paginate(10)->onEachSide(1);
        $modulos = Activity::select('log_name')->distinct()->pluck('log_name');

        return view('livewire.auditoria-table', compact('logs', 'modulos'));
    }
}