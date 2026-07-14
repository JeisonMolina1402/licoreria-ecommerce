<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTicket extends Model
{
    use HasFactory;

    protected $table = 'detalle_tickets';

    // Autorizamos los campos del detalle
    protected $fillable = [
        'ticket_id', 
        'producto_id', 
        'cantidad', 
        'precio_unitario'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}