<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos_caja', function (Blueprint $table) {
            $table->id();
            // Vinculamos el turno con el usuario (vendedor)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->decimal('monto_inicial', 8, 2)->default(0); // El dinero base con el que abre la caja
            $table->decimal('total_efectivo', 8, 2)->default(0); // Lo que vende en el POS
            $table->decimal('total_transferencias', 8, 2)->default(0); // Las reservas web que aprueba
            
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos_caja');
    }
};