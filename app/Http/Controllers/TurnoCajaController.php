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
        // Validamos que el monto inicial sea obligatorio y numérico
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        // Verificamos que no tenga ya un turno abierto
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

        return redirect()->route('tickets.index')->with('success', '¡Turno de caja abierto exitosamente!');
    }

    // Procesa el cierre de la caja con arqueo físico
   // Procesa el cierre de la caja con arqueo físico y digital
    public function cerrar(Request $request)
    {
        $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                 ->where('estado', 'abierto')
                                 ->first();

        if (!$turnoAbierto) {
            return redirect()->back()->withErrors(['error' => 'No tienes ningún turno abierto para cerrar.']);
        }

        // 1. LIMPIEZA DE DATOS (Comas por puntos para evitar errores de MySQL)
        $request->merge([
            'monto_real' => str_replace(',', '.', $request->monto_real),
            'transferencias_real' => str_replace(',', '.', $request->transferencias_real),
        ]);

        // 2. REGLAS DE VALIDACIÓN
        $request->validate([
            'monto_real' => 'required|numeric|min:0',
            'transferencias_real' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // 3. CÁLCULO DE DIFERENCIAS (ARQUEO TOTAL)
        $efectivoEsperado = $turnoAbierto->monto_inicial + $turnoAbierto->total_efectivo;
        $transferenciasEsperadas = $turnoAbierto->total_transferencias;

        $diferenciaEfectivo = $request->monto_real - $efectivoEsperado;
        $diferenciaTransferencias = $request->transferencias_real - $transferenciasEsperadas;
        
        // Sumamos ambas diferencias para saber el balance final del día
        $diferenciaTotal = $diferenciaEfectivo + $diferenciaTransferencias;

        // 4. GUARDAMOS EL CIERRE
        $turnoAbierto->monto_final = $request->monto_real;
        $turnoAbierto->transferencias_final = $request->transferencias_real;
        $turnoAbierto->observaciones = $request->observaciones;
        $turnoAbierto->estado = 'cerrado';
        $turnoAbierto->fecha_cierre = Carbon::now();
        $turnoAbierto->save();

        // 5. MENSAJE PERSONALIZADO
        if ($diferenciaTotal == 0) {
            $mensaje = 'Turno cerrado correctamente. Cuadre perfecto en efectivo y banco.';
        } elseif ($diferenciaTotal < 0) {
            $mensaje = 'Turno cerrado. Se registró un FALTANTE total de $' . number_format(abs($diferenciaTotal), 2);
        } else {
            $mensaje = 'Turno cerrado. Se registró un SOBRANTE total de $' . number_format($diferenciaTotal, 2);
        }

        return redirect()->route('tickets.index')->with('success', $mensaje);
    }
}