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

                    if (!Schema::hasColumn('expedientes', 'asignacion_id')) {
                        $table->foreignId('asignacion_id')->nullable()->constrained('asignaciones');
                    }

                    if (!Schema::hasColumn('expedientes', 'tema_id')) {
                        $table->foreignId('tema_id')->nullable()->constrained('temas');
                    }

                    if (!Schema::hasColumn('expedientes', 'tipo_id')) {
                        $table->foreignId('tipo_id')->nullable()->constrained('tipos');
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
            $table->dropForeign(['asignacion_id']);
            $table->dropForeign(['tema_id']);
            $table->dropForeign(['tipo_id']);

            $table->dropColumn([
                'asignacion_id',
                'tema_id',
                'tipo_id',
                'estado_excepcion',
                'estado'
            ]);
        });
    }
};
