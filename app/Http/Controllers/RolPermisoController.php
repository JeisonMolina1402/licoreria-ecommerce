<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        $permissions = $request->input('permissions', []);
        
        // 1. Obtenemos los nombres de los permisos ANTES de los cambios
        $permisosViejos = $role->permissions->pluck('name')->toArray();
        
        // 2. Sincronizamos (Spatie hace el cambio en BD)
        $role->syncPermissions($permissions);
        
        // 3. Recargamos la relación para ver los permisos DESPUÉS de los cambios
        $role->load('permissions');
        $permisosNuevos = $role->permissions->pluck('name')->toArray();

        // 4. Comparamos para saber exactamente qué se agregó y qué se quitó
        $agregados = array_diff($permisosNuevos, $permisosViejos);
        $removidos = array_diff($permisosViejos, $permisosNuevos);

        // 5. Registramos en auditoría SOLO si hubo algún cambio real
        if (!empty($agregados) || !empty($removidos)) {
            activity('roles_y_permisos')
                ->causedBy(\Illuminate\Support\Facades\Auth::user())
                ->performedOn($role)
                ->event('permisos_actualizados') // Creamos un evento personalizado
                ->withProperties([
                    'agregados' => array_values($agregados),
                    'removidos' => array_values($removidos)
                ])
                ->log('Se modificaron los permisos de acceso de este rol.');
        }

        // Limpiamos la caché de Spatie por seguridad
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Permisos actualizados correctamente para el rol: ' . ucfirst($role->name));
    }
}