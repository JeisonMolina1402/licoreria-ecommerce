<?php

namespace App\Models;

// ¡AQUÍ ESTÁ LA LÍNEA QUE FALTABA IMPORTAR!
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio_compra',
        'precio',
        'stock',
        'imagen',
        'slug',
    ];

    //Esto conecta el Producto con su Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Le dice a Laravel que use esta columna para buscar en las URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
