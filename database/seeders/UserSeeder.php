<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\withoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; //Le decimos que use la tabla de usuarios
use Illuminate\Support\Facades\Hash; // herramienta para encriptar la contraseña
class UserSeeder extends Seeder{
    /**Run the database seeds. */
    public function run(): void
    {
        //creamos la cuenta del administrador
        User::create([
            'name'=>'admin',
            'email'=>'admin@licoreria.com',
            'password'=>Hash::make('admin123'), //contraseña encriptada
            'cedula'=>'1725088825',
            'telefono'=>'0981766228',
            'rol'=>'admin',
        ]);

        //creamos la cuenta del Vendedor para  realizar pruebas

        User::create([
            'name'=> 'vendedor',
            'email'=>'vendedor@licoreria.com',
            'password'=>Hash::make('vendedor123'),
            'cedula'=>'1711111110',
            'telefono'=>'0977777777',
            'rol'=>'vendedor',
        ]);
    }
}

