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
    public $filtroRol = '';
    public $searchUsuario = '';
    public $fechaInicio;
    public $fechaFin;

    // Se ejecuta una sola vez cuando el componente carga en pantalla
    public function mount()
    {
        // Establecemos el día actual por defecto (formato YYYY-MM-DD para el input date)
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
        // Reseteamos los campos de texto y selects
        $this->reset(['modulo', 'filtroRol', 'searchUsuario']);
        // Restauramos las fechas al día de hoy
        $this->fechaInicio = date('Y-m-d');
        $this->fechaFin = date('Y-m-d');
        // Volvemos a la página 1
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::with('causer')->latest();

        if ($this->modulo !== '') {
            $query->where('log_name', $this->modulo);
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

        $logs = $query->paginate(10);
        $modulos = Activity::select('log_name')->distinct()->pluck('log_name');

        return view('livewire.auditoria-table', compact('logs', 'modulos'));
    }
}