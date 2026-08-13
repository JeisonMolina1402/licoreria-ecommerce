<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role; // <-- 1. IMPORTAMOS EL MODELO DE ROLES DE SPATIE

class UserController extends Controller
{
    public function index()
    {
        $personal = User::whereIn('rol', ['admin', 'vendedor'])->orderBy('id', 'desc')->get();
        $clientes = User::where('rol', 'cliente')->orderBy('id', 'desc')->get();

        // 2. Traemos TODOS los roles dinámicos de la base de datos
        $roles = Role::all();

        // 3. Enviamos los roles a la vista
        return view('usuarios.index', compact('personal', 'clientes', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'cedula' => 'nullable|string|unique:users',
            'telefono' => 'nullable|string',
            // 4. Validación Dinámica: Verifica que el rol exista en la tabla de Spatie
            'rol' => 'required|exists:roles,name', 
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'rol' => $request->rol, // Mantenemos tu columna por compatibilidad visual
            'estado' => 'activo',
        ]);

        // 5. MAGIA SPATIE: Le asignamos el rol oficial de seguridad
        $usuario->assignRole($request->rol);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        if ($usuario->id === auth()->id() && $request->rol !== 'admin') {
            return back()->withErrors('Acción bloqueada: No puedes quitarte el rol de administrador a ti mismo.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'cedula' => ['nullable', 'string', Rule::unique('users')->ignore($usuario->id)],
            'telefono' => 'nullable|string',
            // 6. Validación Dinámica
            'rol' => 'required|exists:roles,name', 
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        // 7. MAGIA SPATIE: Sincroniza el rol (borra el viejo y pone el nuevo)
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleEstado(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors('Acción bloqueada: No puedes desactivar tu propia cuenta.');
        }

        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        $mensaje = $usuario->estado === 'activo' ? 'Usuario activado exitosamente.' : 'Usuario bloqueado e inactivo.';
        return redirect()->route('usuarios.index')->with('success', $mensaje);
    }
}