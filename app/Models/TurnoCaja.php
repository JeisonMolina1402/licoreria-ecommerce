<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoCaja extends Model
{
    use HasFactory;

    protected $table = 'turnos_caja';

    protected $fillable = [
        'user_id',
        'monto_inicial',
        'total_efectivo',
        'total_transferencias',
        'estado',
        'fecha_apertura',
        'fecha_cierre'
    ];

    // Para que Laravel maneje estas fechas como objetos Carbon
    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}