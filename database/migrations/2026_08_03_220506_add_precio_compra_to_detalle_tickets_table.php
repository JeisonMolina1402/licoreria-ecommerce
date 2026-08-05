<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detalle_tickets', function (Blueprint $table) {
            // Agregamos la columna justo después del precio_unitario
            $table->decimal('precio_compra', 8, 2)->default(0)->after('precio_unitario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_tickets', function (Blueprint $table) {
            $table->dropColumn('precio_compra');
        });
    }
};
