<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    /**
     * Muestra la lista de roles en el panel.
     */
    public function index()
    {
        // Traemos todos los roles de Spatie con sus permisos precargados
        $roles = Role::with('permissions')->orderBy('id', 'asc')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Guarda un nuevo rol en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Validamos que el nombre sea único en la tabla de roles
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        Role::create(['name' => strtolower($request->name)]);

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Actualiza un rol existente.
     */
    public function update(Request $request, Role $role)
    {
        // Protegemos el rol super admin para que nadie le cambie el nombre por error
        if ($role->name === 'admin') {
            return back()->withErrors('Acción bloqueada: No puedes modificar el nombre del rol Administrador Principal.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)]
        ]);

        $role->update(['name' => strtolower($request->name)]);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina un rol.
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'admin' || $role->name === 'cliente') {
            return back()->withErrors('Acción bloqueada: No puedes eliminar roles del sistema críticos (admin/cliente).');
        }

        // Si hay usuarios asignados a este rol, no deberíamos borrarlo tan fácil
        if ($role->users()->count() > 0) {
            return back()->withErrors('No se puede eliminar el rol porque tiene usuarios asignados. Quita el rol a los usuarios primero.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado del sistema.');
    }
}