<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Producto extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio_compra',
        'precio',
        'stock',
        'imagen',
        'slug',
        'estado',
    ];

    // Reglas de la auditoría
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'precio_compra', 'precio', 'stock', 'estado']) // Campos a vigilar
            ->logOnlyDirty() // Solo registrar si el valor realmente cambió
            ->dontSubmitEmptyLogs() // No guardar registros vacíos
            ->useLogName('inventario'); // Etiqueta para identificar de dónde viene el cambio
    }

    //Esto conecta el Producto con su Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación: Un producto puede tener muchos detalles de tickets (ventas).
     */
    public function detalles()
    {
        return $this->hasMany(DetalleTicket::class, 'producto_id');
    }

    // Le dice a Laravel que use esta columna para buscar en las URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
