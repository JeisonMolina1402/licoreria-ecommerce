<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        // Separamos al personal de los clientes en dos variables diferentes
        $personal = User::whereIn('rol', ['admin', 'vendedor'])->orderBy('id', 'desc')->get();
        $clientes = User::where('rol', 'cliente')->orderBy('id', 'desc')->get();

        return view('usuarios.index', compact('personal', 'clientes'));
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos entrantes
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'cedula' => 'nullable|string|unique:users',
            'telefono' => 'nullable|string',
            'rol' => ['required', Rule::in(['admin', 'vendedor', 'cliente'])],
        ]);

        // 2. Creamos el usuario asegurándonos de encriptar la contraseña
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
            'estado' => 'activo', // Por defecto nacen activos
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualiza los datos y permisos de un usuario existente.
     */
    public function update(Request $request, User $usuario)
    {
        // Evitamos que te quites el rol de admin a ti mismo
        if ($usuario->id === auth()->id() && $request->rol !== 'admin') {
            return back()->withErrors('Acción bloqueada: No puedes quitarte el rol de administrador a ti mismo.');
        }

        // Validamos ignorando el ID del usuario actual para que el 'unique' del email y cédula no falle
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'cedula' => ['nullable', 'string', Rule::unique('users')->ignore($usuario->id)],
            'telefono' => 'nullable|string',
            'rol' => ['required', Rule::in(['admin', 'vendedor', 'cliente'])],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'rol' => $request->rol,
        ];

        // Si el administrador escribió una nueva contraseña en el formulario, la actualizamos
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Cambia el estado de Activo a Inactivo y viceversa (Soft-Deactivate).
     */
    public function toggleEstado(User $usuario)
    {
        // PROGRAMACIÓN DEFENSIVA: Un admin no puede desactivar su propia cuenta en un ataque de pánico o error
        if ($usuario->id === auth()->id()) {
            return back()->withErrors('Acción bloqueada: No puedes desactivar tu propia cuenta.');
        }

        // Intercambiador lógico (Ternario)
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        $mensaje = $usuario->estado === 'activo' ? 'Usuario activado exitosamente.' : 'Usuario bloqueado e inactivo.';
        
        return redirect()->route('usuarios.index')->with('success', $mensaje);
    }
}