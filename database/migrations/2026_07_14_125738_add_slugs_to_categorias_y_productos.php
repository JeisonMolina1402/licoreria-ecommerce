<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            // Añadimos el slug después del nombre. unique() es vital para que las URLs no choquen.
            $table->string('slug')->nullable()->unique()->after('nombre');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};