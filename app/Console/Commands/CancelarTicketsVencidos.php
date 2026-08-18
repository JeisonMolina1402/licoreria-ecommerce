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
        // 1. Calculamos la hora límite hace 10 minutos exactos
        $limite = Carbon::now()->subMinutes(1);

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
                        $producto = $detalle->producto;
                        $stockAnterior = $producto->stock;
                        
                        // Apagamos log automático
                        $producto->disableLogging();
                        $producto->stock = $producto->stock + $detalle->cantidad;
                        $producto->save();
                        $producto->enableLogging();

                        // Creamos UN SOLO registro maestro
                        activity('inventario') 
                            ->performedOn($producto)
                            ->event('devolucion_automatica')
                            ->withProperties([
                                'old' => ['stock' => $stockAnterior],
                                'attributes' => ['stock' => $producto->stock]
                            ])
                            ->log("{$detalle->cantidad} producto(s) devuelto(s) al stock automáticamente por vencimiento de tiempo del Ticket {$ticket->codigo_reserva}");
                    }
                }
            });

            // Mostramos un mensaje en la consola por cada ticket cancelado
            $this->info("¡Ticket {$ticket->codigo_reserva} cancelado automáticamente! Stock devuelto.");
        }
    }
}