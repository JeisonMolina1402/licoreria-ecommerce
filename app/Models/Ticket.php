<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Aquí le decimos a Laravel qué columnas SÍ puede llenar automáticamente
    protected $fillable = [
        'user_id', 
        'codigo_reserva', 
        'estado', 
        'total', 
        'comprobante_whatsapp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTicket::class);
    }
}