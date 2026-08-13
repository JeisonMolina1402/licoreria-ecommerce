<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar la caché de Spatie (Obligatorio siempre que se crean roles/permisos por código)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 2. CREACIÓN DE PERMISOS BÁSICOS
        // ==========================================
        // Usamos firstOrCreate para que no dé error si corres el seeder dos veces
        $permisos = [
            'ver dashboard',
            'ver auditoria',
            'gestionar inventario',
            'gestionar tickets',
            'gestionar reportes',
            'gestionar usuarios',
            'gestionar roles y permisos'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // ==========================================
        // 3. CREACIÓN DE ROLES Y ASIGNACIÓN DE PERMISOS
        // ==========================================
        
        // A) ROL ADMIN: Tiene absolutamente todos los permisos
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // B) ROL VENDEDOR: Solo tiene acceso a ciertas áreas operativas
        $roleVendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $roleVendedor->syncPermissions([
            'ver dashboard',
            'gestionar inventario',
            'gestionar tickets'
        ]);

        // C) ROL CLIENTE: No necesita permisos de panel, solo el rol para identificarlo en la tienda
        $roleCliente = Role::firstOrCreate(['name' => 'cliente']);


        // ==========================================
        // 4. MIGRACIÓN DE USUARIOS EXISTENTES
        // ==========================================
        // Traemos todos los usuarios actuales de tu base de datos
        $usuarios = User::all();

        foreach ($usuarios as $user) {
            // Leemos tu columna antigua 'rol' y le asignamos el Rol oficial de Spatie
            if ($user->rol === 'admin') {
                $user->assignRole('admin');
            } elseif ($user->rol === 'vendedor') {
                $user->assignRole('vendedor');
            } elseif ($user->rol === 'cliente') {
                $user->assignRole('cliente');
            }
        }
    }
}