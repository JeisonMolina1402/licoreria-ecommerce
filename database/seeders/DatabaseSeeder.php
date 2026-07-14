<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Aquí le decimos al "director de orquesta" qué archivos debe ejecutar
        $this->call([
            UserSeeder::class,
            // A futuro, si hacemos un CategoriaSeeder o ProductoSeeder, los agregaremos aquí abajo.
        ]);
    }
}