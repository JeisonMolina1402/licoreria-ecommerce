<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    use LogsActivity;

    /**
     * Los atributos que se pueden asignar de forma masiva (Formularios).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'telefono',
        'rol',
        'estado',
        'avatar',
        'direccion',
    ];

    /**
     * Los atributos que deben ocultarse (Seguridad).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Reglas de auditoría para usuarios (Enfocado en Privacidad)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Solo auditamos cambios administrativos críticos.
            // Ignoramos por completo nombre, email, cedula, telefono, direccion y avatar.
            ->logOnly(['rol', 'estado'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            // Traducción dinámica del evento
            ->setDescriptionForEvent(function(string $eventName) {
                $acciones = [
                    'created' => 'creado',
                    'updated' => 'actualizado',
                    'deleted' => 'eliminado'
                ];
                $accion = $acciones[$eventName] ?? $eventName;
                return "El usuario ha sido {$accion}";
            })
            ->useLogName('usuarios'); // Aparecerá bajo el módulo "USUARIOS"
    }
}
