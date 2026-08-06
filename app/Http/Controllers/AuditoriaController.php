<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Importamos el modelo oficial de Spatie
use Spatie\Activitylog\Models\Activity;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        // Protegemos la ruta
        $this->middleware('auth');
    }

    public function index()
    {
        // Traemos el historial ordenado por fecha descendente
        // 'with(causer)' trae automáticamente los datos del usuario que hizo el cambio
        $logs = Activity::with('causer')->latest()->paginate(15);
        
        return view('auditoria.index', compact('logs'));
    }
}