<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permission::orderBy('id', 'desc')->get();
        return view('permisos.index', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        // SOLUCIÓN: Asignar a la variable $permission
        $permission = Permission::create(['name' => strtolower($request->name)]);

        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->event('created')
            ->withProperties(['attributes' => ['name' => $permission->name]])
            ->log('Se ha creado un nuevo permiso.');

        return redirect()->route('permisos.index')->with('success', 'Permiso creado exitosamente.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)]
        ]);

        $oldName = $permission->name;
        $permission->update(['name' => strtolower($request->name)]);

        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->event('updated')
            ->withProperties([
                'old' => ['name' => $oldName],
                'attributes' => ['name' => $permission->name]
            ])
            ->log('Se ha actualizado el nombre de un permiso.');

        return redirect()->route('permisos.index')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permission $permission)
    {
        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->event('deleted')
            ->withProperties(['old' => ['name' => $permission->name]])
            // SOLUCIÓN: Cambiado a "permiso"
            ->log('Se ha eliminado un permiso.');
            
        $permission->delete();

        return redirect()->route('permisos.index')->with('success', 'Permiso eliminado del sistema.');
    }
}