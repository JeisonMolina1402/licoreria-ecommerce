<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCreadoClienteNotification extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    // Usaremos correo (mail) y la campanita (database)
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    // DISEÑO DEL CORREO
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('¡Reserva Confirmada! - Licorería Web Store')
                    // Aquí llamamos a la vista Blade que crearemos en el siguiente paso
                    ->view('emails.ticket_creado', ['ticket' => $this->ticket]); 
    }

    // DATOS PARA LA CAMPANITA
    public function toDatabase($notifiable): array
    {
        return [
            'titulo' => 'Reserva Confirmada',
            'mensaje' => 'Tu pedido #' . $this->ticket->codigo_reserva . ' se ha registrado. Tienes 10 minutos para pagar.',
            'url' => route('tienda.exito', $this->ticket->id),
            'icono' => 'fa-solid fa-circle-check text-success'
        ];
    }
}