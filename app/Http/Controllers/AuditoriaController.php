<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index()
    {
        return view('auditoria.index');
    }

    public function exportarPdf(\Illuminate\Http\Request $request)
    {
        $query = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])->latest();

        // 1. Filtro por Módulo
        if ($request->filled('modulo')) {
            $query->where('log_name', $request->modulo);
        }

        // 2. Filtro por Rol
        if ($request->filled('filtroRol')) {
            $query->whereHasMorph('causer', [\App\Models\User::class], function ($q) use ($request) {
                $q->where('rol', $request->filtroRol);
            });
        }

        // 3. Filtro por Búsqueda de Usuario (Nombre o Cédula)
        if ($request->filled('searchUsuario')) {
            $query->whereHasMorph('causer', [\App\Models\User::class], function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->searchUsuario . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $request->searchUsuario . '%');
            });
        }

        // 4. Filtro por Fechas
        if ($request->filled('fechaInicio') && $request->filled('fechaFin')) {
            $query->whereBetween('created_at', [$request->fechaInicio . ' 00:00:00', $request->fechaFin . ' 23:59:59']);
        }

        $logs = $query->get();

        // Generar el PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('auditoria.pdf', compact('logs'))
                ->setPaper('a4', 'landscape'); // Formato horizontal para que quepa bien el texto

        return $pdf->download('Reporte_Auditoria_' . date('d-m-Y') . '.pdf');
    }
}