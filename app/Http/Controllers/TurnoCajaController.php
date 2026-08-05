<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TurnoCaja;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TurnoCajaController extends Controller
{

    // Procesa la apertura de la caja
    public function abrir(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        // Verificamos por seguridad que no tenga ya un turno abierto
        $turnoExistente = TurnoCaja::where('user_id', Auth::id())
                                   ->where('estado', 'abierto')
                                   ->first();

        if ($turnoExistente) {
            return redirect()->back()->withErrors(['error' => 'Ya tienes un turno de caja abierto.']);
        }

        TurnoCaja::create([
            'user_id' => Auth::id(),
            'monto_inicial' => $request->monto_inicial,
            'total_efectivo' => 0,
            'total_transferencias' => 0,
            'estado' => 'abierto',
            'fecha_apertura' => Carbon::now(),
        ]);

        return redirect()->route('tickets.index')->with('success', 'Turno de caja abierto exitosamente. ¡Buen turno!');
    }

    // Procesa el cierre de la caja
    public function cerrar(Request $request)
    {
        $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                 ->where('estado', 'abierto')
                                 ->first();

        if (!$turnoAbierto) {
            return redirect()->back()->withErrors(['error' => 'No tienes ningún turno abierto para cerrar.']);
        }

        $turnoAbierto->estado = 'cerrado';
        $turnoAbierto->fecha_cierre = Carbon::now();
        $turnoAbierto->save();

        return redirect()->route('tickets.index')->with('success', 'Turno de caja cerrado correctamente. Has finalizado tu jornada.');
    }
}