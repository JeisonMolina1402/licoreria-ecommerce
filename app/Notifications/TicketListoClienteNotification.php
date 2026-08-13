<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketListoClienteNotification extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database']; // Solo campanita
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo' => '¡Tu pedido está listo!',
            'mensaje' => 'El pedido #' . $this->ticket->codigo_reserva . ' está empacado y listo para retirar en el local.',
            'url' => route('tienda.exito', $this->ticket->id),
            'icono' => 'fa-solid fa-box text-purple'
        ];
    }
}