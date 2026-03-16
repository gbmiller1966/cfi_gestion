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
        Schema::table('expedientes', function (Blueprint $table) {

                    if (!Schema::hasColumn('expedientes', 'asignacion_presupuestaria_id')) {
                        $table->foreignId('asignacion_presupuestaria_id')->nullable()->constrained('asignaciones_presupuestarias');
                    }

                    if (!Schema::hasColumn('expedientes', 'tema_estrategico_id')) {
                        $table->foreignId('tema_estrategico_id')->nullable()->constrained('temas_estrategicos');
                    }

                    if (!Schema::hasColumn('expedientes', 'tipo_contrato_id')) {
                        $table->foreignId('tipo_contrato_id')->nullable()->constrained('tipos_contratos');
                    }

                    if (!Schema::hasColumn('expedientes', 'estado_excepcion')) {
                        $table->string('estado_excepcion')->nullable();
                    }

                    if (!Schema::hasColumn('expedientes', 'estado')) {
                        $table->string('estado')->default('Ingresado al CFI');
                    }

                });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropForeign(['asignacion_presupuestaria_id']);
            $table->dropForeign(['tema_estrategico_id']);
            $table->dropForeign(['tipo_contrato_id']);

            $table->dropColumn([
                'asignacion_presupuestaria_id',
                'tema_estrategico_id',
                'tipo_contrato_id',
                'estado_excepcion',
                'estado'
            ]);
        });
    }
};
