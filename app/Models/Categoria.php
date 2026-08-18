<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Categoria extends Model
{
    use HasFactory;
    use LogsActivity;


    protected $fillable = ['id', 'nombre', 'descripcion', 'slug'];
    // Le dice a Laravel que use esta columna para buscar en las URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // NUEVO: Reglas de auditoría para Categorías
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'descripcion']) // Vigilar estos campos
            ->logOnlyDirty() // Solo registrar si hay cambios
            ->dontSubmitEmptyLogs()
            ->useLogName('categorias'); // Se mostrará bajo el módulo "CATEGORIAS"
    }
}
