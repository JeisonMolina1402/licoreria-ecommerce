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
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones_apertura' => 'nullable|string|max:500' // <-- NUEVO
        ]);

        $turnoExistente = TurnoCaja::where('user_id', Auth::id())->where('estado', 'abierto')->first();

        if ($turnoExistente) {
            return redirect()->back()->withErrors(['error' => 'Ya tienes un turno de caja abierto.']);
        }

        TurnoCaja::create([
            'user_id' => Auth::id(),
            'monto_inicial' => $request->monto_inicial,
            'total_efectivo' => 0,
            'total_transferencias' => 0,
            'estado' => 'abierto',
            'observaciones_apertura' => $request->observaciones_apertura, // <-- NUEVO
            'fecha_apertura' => Carbon::now(),
        ]);

        return redirect()->route('tickets.index')->with('success', '¡Turno de caja abierto exitosamente!');
    }

    // Procesa el cierre de la caja con arqueo y comprobante
    public function cerrar(Request $request)
    {
        $turnoAbierto = TurnoCaja::where('user_id', Auth::id())->where('estado', 'abierto')->first();

        if (!$turnoAbierto) {
            return redirect()->back()->withErrors(['error' => 'No tienes ningún turno abierto para cerrar.']);
        }

        $request->merge([
            'monto_real' => str_replace(',', '.', $request->monto_real ?? ''),
            'transferencias_real' => str_replace(',', '.', $request->transferencias_real ?? ''),
        ]);

        $request->validate([
            'monto_real' => 'required|numeric|min:0',
            'transferencias_real' => 'required|numeric|min:0',
            'observaciones' => 'required|string|max:500',
            'comprobante_deposito' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // <-- REGLA FOTO
        ]);

        // Guardar foto del comprobante si existe
        $rutaComprobante = null;
        if ($request->hasFile('comprobante_deposito')) {
            $nombreImagen = 'deposito_' . time() . '.' . $request->comprobante_deposito->extension();
            $request->comprobante_deposito->move(public_path('uploads/caja'), $nombreImagen);
            $rutaComprobante = 'uploads/caja/' . $nombreImagen; 
        }

        $efectivoEsperado = $turnoAbierto->monto_inicial + $turnoAbierto->total_efectivo;
        $transferenciasEsperadas = $turnoAbierto->total_transferencias;

        $diferenciaEfectivo = $request->monto_real - $efectivoEsperado;
        $diferenciaTransferencias = $request->transferencias_real - $transferenciasEsperadas;
        $diferenciaTotal = $diferenciaEfectivo + $diferenciaTransferencias;

        $turnoAbierto->monto_final = $request->monto_real;
        $turnoAbierto->transferencias_final = $request->transferencias_real;
        $turnoAbierto->observaciones = $request->observaciones;
        if($rutaComprobante) $turnoAbierto->comprobante_deposito = $rutaComprobante; // <-- ASIGNAR FOTO
        $turnoAbierto->estado = 'cerrado';
        $turnoAbierto->fecha_cierre = Carbon::now();
        $turnoAbierto->save();

        if ($diferenciaTotal == 0) {
            $mensaje = 'Turno cerrado correctamente. Cuadre perfecto en efectivo y banco.';
        } elseif ($diferenciaTotal < 0) {
            $mensaje = 'Turno cerrado. Se registró un FALTANTE de $' . number_format(abs($diferenciaTotal), 2);
        } else {
            $mensaje = 'Turno cerrado. Se registró un SOBRANTE de $' . number_format($diferenciaTotal, 2);
        }

        return redirect()->route('tickets.index')->with('success', $mensaje);
    }
}