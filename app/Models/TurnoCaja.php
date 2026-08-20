<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TurnoCaja extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'turnos_caja';

    protected $fillable = [
        'user_id',
        'monto_inicial',
        'total_efectivo',
        'total_transferencias',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones_apertura',
        'comprobante_deposito'
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Le decimos qué columnas queremos vigilar
            ->logOnly([
                'monto_inicial',
                'total_efectivo',
                'total_transferencias',
                'monto_final',
                'transferencias_final',
                'estado',
                'observaciones',
                'observaciones_apertura',
                'comprobante_deposito'
            ])
            // Solo guarda si realmente hubo un cambio
            ->logOnlyDirty()
            // Evita guardar registros vacíos
            ->dontSubmitEmptyLogs()
            // Le damos el nombre del módulo para que aparezca bonito en tu UI
            ->useLogName('caja');
    }
}
