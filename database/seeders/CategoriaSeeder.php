<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            'Whisky',
            'Ron',
            'Vino',
            'Tequila',
            'Vodka',
            'Cervezas',
            'Aguardiente',
            'Champagne y Espumantes',
            'Bebidas sin Alcohol',
            'Snacks y Complementos'
        ];

        foreach ($categorias as $categoria) {
            // updateOrCreate evita que se dupliquen si corres el comando dos veces
            Categoria::updateOrCreate(
                ['nombre' => $categoria],
                ['descripcion' => 'Categoría de ' . $categoria]
            );
        }
    }
}