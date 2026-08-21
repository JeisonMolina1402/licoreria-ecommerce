<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo agregamos lo que nos falta viendo tu imagen
            $table->string('avatar')->nullable()->after('email');
            $table->text('direccion')->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'direccion']);
        });
    }
};