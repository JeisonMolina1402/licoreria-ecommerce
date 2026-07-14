<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usamos una sentencia SQL directa para modificar el ENUM sin borrar la tabla
        DB::statement("ALTER TABLE tickets MODIFY COLUMN estado ENUM('pendiente', 'pagado', 'entregado', 'cancelado') DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN estado ENUM('pendiente', 'pagado', 'cancelado') DEFAULT 'pendiente'");
    }
};