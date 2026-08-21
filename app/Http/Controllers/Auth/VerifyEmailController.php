<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Evaluamos el rol del usuario
        $urlDestino = $request->user()->hasAnyRole(['admin', 'vendedor']) 
            ? route('dashboard', absolute: false) 
            : '/'; // Los clientes van a la raíz de la tienda

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($urlDestino.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // 🔥 Usamos la variable $urlDestino en lugar de forzar 'dashboard'
        return redirect()->intended($urlDestino.'?verified=1');
    }
}