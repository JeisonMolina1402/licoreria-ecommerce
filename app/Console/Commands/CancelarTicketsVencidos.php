<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelarTicketsVencidos extends Command
{
    /**
     * El nombre y firma del comando en la terminal.
     */
    protected $signature = 'tickets:cancelar-vencidos';

    /**
     * La descripción que aparecerá en la consola.
     */
    protected $description = 'Cancela los tickets pendientes con más de 10 minutos y devuelve el stock al inventario.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Calculamos la hora límite (hace 10 minutos exactos)
        $limite = Carbon::now()->subMinutes(10);

        // 2. Buscamos tickets "pendientes" creados antes de esa hora límite
        $ticketsVencidos = Ticket::where('estado', 'pendiente')
                                 ->where('created_at', '<=', $limite)
                                 ->with('detalles.producto') // Traemos los productos para devolver el stock
                                 ->get();

        if ($ticketsVencidos->isEmpty()) {
            $this->info('Todo en orden. No hay tickets vencidos para cancelar.');
            return;
        }

        // 3. Si encontramos tickets vencidos, los procesamos uno por uno
        foreach ($ticketsVencidos as $ticket) {
            
            // Usamos una transacción por seguridad
            DB::transaction(function () use ($ticket) {
                
                // Cambiamos el estado a cancelado
                $ticket->estado = 'cancelado';
                $ticket->save();

                // Recorremos los detalles de ese ticket para devolver las botellas
                foreach ($ticket->detalles as $detalle) {
                    if ($detalle->producto) {
                        // "increment" hace lo opuesto a "decrement", suma la cantidad al stock
                        $detalle->producto->increment('stock', $detalle->cantidad);
                    }
                }
            });

            // Mostramos un mensaje en la consola por cada ticket cancelado
            $this->info("¡Ticket {$ticket->codigo_reserva} cancelado automáticamente! Stock devuelto.");
        }
    }
}