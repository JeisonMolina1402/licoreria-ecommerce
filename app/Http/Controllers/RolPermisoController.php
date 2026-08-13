<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolPermisoController extends Controller
{
    /**
     * Muestra la vista con los checkboxes para asignar permisos a un rol.
     */
    public function index($id)
    {
        $rol = Role::findOrFail($id);
        
        // Bloqueo de seguridad: El rol Admin siempre tiene todo, no hace falta editarlo
        if ($rol->name === 'admin') {
            return redirect()->route('roles.index')->withErrors('El rol Administrador ya tiene todos los permisos por defecto.');
        }

        $permissions = Permission::all();
        
        // Obtenemos los IDs de los permisos que este rol YA tiene asignados (para marcar los checkboxes)
        $rolePermissions = $rol->permissions->pluck('id')->toArray();

        return view('roles.permisos', compact('rol', 'permissions', 'rolePermissions'));
    }

    /**
     * Sincroniza (guarda) los nuevos permisos marcados en la base de datos.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->withErrors('No se pueden modificar los permisos del Administrador.');
        }

        // Si el request no trae la variable 'permissions', significa que desmarcaron todo.
        $permissions = $request->input('permissions', []);
        
        // syncPermissions es magia de Spatie: quita los viejos y pone los nuevos de golpe
        $role->syncPermissions($permissions);

        // Limpiamos la caché de Spatie por seguridad
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Permisos actualizados correctamente para el rol: ' . ucfirst($role->name));
    }
}