<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id', 'asc')->get();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        // SOLUCIÓN: Asignar la creación a la variable $role
        $role = Role::create(['name' => strtolower($request->name)]);

        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->event('created')
            ->withProperties(['attributes' => ['name' => $role->name]])
            ->log('Se ha creado un nuevo rol.');

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            return back()->withErrors('Acción bloqueada: No puedes modificar el nombre del rol Administrador Principal.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)]
        ]);

        $oldName = $role->name;
        // Se hace el update UNA sola vez
        $role->update(['name' => strtolower($request->name)]);

        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->event('updated')
            ->withProperties([
                'old' => ['name' => $oldName],
                'attributes' => ['name' => $role->name]
            ])
            ->log('Se ha actualizado el nombre de un rol.');

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin' || $role->name === 'cliente') {
            return back()->withErrors('Acción bloqueada: No puedes eliminar roles del sistema críticos (admin/cliente).');
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors('No se puede eliminar el rol porque tiene usuarios asignados. Quita el rol a los usuarios primero.');
        }

        activity('roles_y_permisos')
            ->causedBy(Auth::user())
            ->performedOn($role)
            ->event('deleted')
            ->withProperties(['old' => ['name' => $role->name]])
            ->log('Se ha eliminado un rol.');

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado del sistema.');
    }
}