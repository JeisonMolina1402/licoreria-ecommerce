<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Añadimos la columna. Por defecto será 'transferencia' para no dañar los tickets web viejos
            $table->enum('metodo_pago', ['efectivo', 'transferencia'])->default('transferencia')->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};