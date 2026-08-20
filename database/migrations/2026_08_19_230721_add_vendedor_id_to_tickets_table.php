<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Agregamos la columna vendedor_id, permitiendo que sea nula (para cuando el cliente recién crea el pedido web)
            $table->unsignedBigInteger('vendedor_id')->nullable()->after('user_id');
            
            // Creamos la relación (llave foránea) con la tabla users
            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn('vendedor_id');
        });
    }
};