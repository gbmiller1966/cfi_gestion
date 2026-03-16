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
            Schema::create('expediente_informes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('expediente_id')->constrained()->cascadeOnDelete();
                
                // Asumiendo que tu tabla maestra de informes se llama 'informes_maestros' o similar
                // Cambiá 'informes_maestros' por el nombre real de tu tabla si es distinto
                $table->foreignId('informe_id')->constrained('informes_maestros'); 
                
                $table->integer('meses_pactados')->nullable();
                $table->date('fecha_limite')->nullable();
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente_informes');
    }
};
