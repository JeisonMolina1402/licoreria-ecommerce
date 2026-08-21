<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsuariosTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscar = '';
    public $filtro_rol = '';

    // Si escribimos en el buscador, regresamos a la página 1 automáticamente
    public function updatingBuscar()
    {
        $this->resetPage();
    }

    // Si cambiamos el filtro de rol, regresamos a la página 1
    public function updatingFiltroRol()
    {
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->reset(['buscar', 'filtro_rol']);
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        // 1. Filtro por Nombre, Cédula o Correo
        if (!empty($this->buscar)) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . $this->buscar . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $this->buscar . '%')
                  ->orWhere('email', 'LIKE', '%' . $this->buscar . '%');
            });
        }

        // 2. Filtro por Rol
        if (!empty($this->filtro_rol)) {
            $query->where('rol', $this->filtro_rol);
        }

        // 3. Paginación compacta (usamos onEachSide(1) como acordamos)
        $usuarios = $query->orderBy('id', 'desc')->paginate(10)->onEachSide(1);
        $roles = Role::all();

        return view('livewire.usuarios-table', compact('usuarios', 'roles'));
    }
}