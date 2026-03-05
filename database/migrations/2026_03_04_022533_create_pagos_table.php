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
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_depa')->constrained('departamentos');
        $table->decimal('monto', 10, 2);
        $table->integer('id_tipo'); // Puedes hacerlo foreignId si creas la tabla tipos_pago
        $table->date('fecha');
        $table->integer('id_motivo'); // Puedes hacerlo foreignId si creas la tabla motivos
        $table->string('descripcion')->nullable();
        $table->string('comprobante')->nullable(); // Para la ruta del archivo
        $table->boolean('efectuado')->default(false);
        $table->foreignId('id_reporte')->nullable()->constrained('reportes');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
