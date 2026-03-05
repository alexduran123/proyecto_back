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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id(); // ID Serial para Postgres
            
            // Relación con el usuario que crea el reporte (R03/R04)
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
            
            // Contenido del reporte
            $table->text('reporte');
            
            // Fecha con timestamp (usando el formato de Postgres)
            $table->timestamp('fecha')->useCurrent();
            
            // Estado para el control en el Dashboard (Alertas)
            $table->enum('status', ['pendiente', 'urgente', 'resuelto'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};