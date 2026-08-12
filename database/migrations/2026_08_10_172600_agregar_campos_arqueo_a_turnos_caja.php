<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos_caja', function (Blueprint $table) {
            $table->decimal('monto_final', 10, 2)->nullable()->after('total_transferencias');
            $table->decimal('transferencias_final', 10, 2)->nullable()->after('monto_final');
            $table->text('observaciones')->nullable()->after('transferencias_final');
        });
    }

    public function down(): void
    {
        Schema::table('turnos_caja', function (Blueprint $table) {
            $table->dropColumn(['monto_final', 'transferencias_final', 'observaciones']);
        });
    }
};