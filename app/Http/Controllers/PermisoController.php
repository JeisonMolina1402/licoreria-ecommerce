<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

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

        Permission::create(['name' => strtolower($request->name)]);

        return redirect()->route('permisos.index')->with('success', 'Permiso creado exitosamente.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)]
        ]);

        $permission->update(['name' => strtolower($request->name)]);

        return redirect()->route('permisos.index')->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permission $permission)
    {
        // Spatie se encarga de quitar este permiso de todos los roles automáticamente al borrarlo
        $permission->delete();

        return redirect()->route('permisos.index')->with('success', 'Permiso eliminado del sistema.');
    }
}