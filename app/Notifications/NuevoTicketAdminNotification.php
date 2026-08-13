<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevoTicketAdminNotification extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo' => 'Nueva Reserva Web',
            'mensaje' => 'El cliente ' . ($this->ticket->user->name ?? 'Desconocido') . ' ha creado el pedido #' . $this->ticket->codigo_reserva,
            'url' => route('tickets.index'), // Redirige al panel de tickets
            'icono' => 'fa-solid fa-bell text-warning'
        ];
    }
}