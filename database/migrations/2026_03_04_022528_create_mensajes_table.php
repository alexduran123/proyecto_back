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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitente')->constrained('personas');
            $table->foreignId('destinatario')->constrained('personas');
            $table->foreignId('id_depaA')->nullable()->constrained('departamentos');
            $table->foreignId('id_depaB')->nullable()->constrained('departamentos');
            $table->text('mensaje');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
