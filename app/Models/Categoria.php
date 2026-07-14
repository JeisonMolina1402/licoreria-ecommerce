<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;


    protected $fillable = ['id', 'nombre', 'descripcion', 'slug'];
    // Le dice a Laravel que use esta columna para buscar en las URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
