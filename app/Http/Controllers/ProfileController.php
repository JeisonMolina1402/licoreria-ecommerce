<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; // 🔥 IMPORTANTE: Necesario para manejar archivos

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Llena los datos de texto (name, email, cedula, telefono, direccion)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 🔥 LÓGICA PARA PROCESAR LA FOTO DE PERFIL
        if ($request->hasFile('avatar')) {
            // Si el usuario ya tenía una foto previa, la borramos del servidor para no acumular basura
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Guardamos la nueva imagen en la carpeta 'avatars' dentro del disco público
            $path = $request->file('avatar')->store('avatars', 'public');
            
            // Asignamos la ruta generada al usuario
            $user->avatar = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 🔥 Opcional pero recomendado: Si el usuario elimina su cuenta, borramos su foto del servidor
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}