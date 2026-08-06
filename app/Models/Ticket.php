<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use HasFactory;

    use LogsActivity;

    // Aquí le decimos a Laravel qué columnas SÍ puede llenar automáticamente
    protected $fillable = [
        'user_id', 
        'codigo_reserva', 
        'estado', 
        'metodo_pago',
        'total', 
        'comprobante_whatsapp'
    ];

    // 3. Reglas de auditoría
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['estado', 'total', 'metodo_pago']) 
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ventas');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTicket::class);
    }
}