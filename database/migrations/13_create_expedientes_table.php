<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('gde_numero')->nullable();
            $table->text('objeto');

            // --- Montos ---
            $table->decimal('monto_solicitud_provincial', 15, 2)->default(0);
            $table->decimal('monto_convenido', 15, 2)->default(0);
            $table->decimal('monto_cfi', 15, 2)->default(0);
            $table->decimal('monto_imputado', 15, 2)->default(0);

            // --- Relaciones de Propiedad y Estructura ---
            $table->foreignId('user_id')->constrained('users'); // Técnico dueño
            $table->foreignId('direccion_id')->constrained('direcciones');
            $table->foreignId('area_id')->constrained('areas');

            // --- Relaciones Maestras ---
            $table->foreignId('region_id')->constrained('regiones');
            $table->foreignId('provincia_id')->constrained('provincias');
            $table->foreignId('localidad_id')->constrained('localidades');
            $table->foreignId('contraparte_id')->constrained('contrapartes');
            $table->foreignId('asignacion_id')->nullable()->constrained('asignaciones');
            $table->foreignId('tema_id')->nullable()->constrained('temas');
            $table->foreignId('tipo_id')->nullable()->constrained('tipos');
            $table->foreignId('estado_id')->nullable()->constrained('estados');


            // --- Circuito de Tiempos (16 Etapas) ---
            $table->timestamp('f_ingreso_cfi')->nullable();
            $table->timestamp('f_ingreso_area')->nullable();
            $table->timestamp('f_derivacion_tecnico')->nullable();
            $table->timestamp('f_elevacion_tdr')->nullable();
            $table->timestamp('f_firma_jefe_tdr')->nullable();
            $table->timestamp('f_firma_director_tdr')->nullable();
            $table->string('gde_tdr')->nullable();

            $table->timestamp('f_derivacion_compras')->nullable();
            $table->date('f_inicio_contrato')->nullable();
            $table->date('f_fin_contrato')->nullable();

            $table->timestamp('f_ingreso_informe_final')->nullable();
            $table->timestamp('f_aprobacion_contraparte')->nullable();
            $table->timestamp('f_aprobacion_jefe_tecnico')->nullable();
            $table->timestamp('f_aprobacion_director')->nullable();
            $table->string('gde_aprobacion_dir')->nullable();

            $table->timestamp('f_pase_gestion')->nullable();
            $table->timestamp('f_aprobacion_sec_gen')->nullable();
            $table->string('gde_sec_gen')->nullable();

            $table->timestamp('f_envio_biblioteca')->nullable();
            $table->string('gde_biblioteca')->nullable();

            $table->timestamp('f_envio_archivo')->nullable();
            $table->string('gde_archivo')->nullable();

            $table->integer('etapa_actual')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Proveedores (Pivot)
        Schema::create('expediente_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
        });

        // Colaboradores (Pivot)
        Schema::create('expediente_colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });

        // Informes Pactados (Ajustada con meses_pactados)
/*         Schema::create('expediente_informes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->foreignId('informe_id')->constrained('informes_maestros')->onDelete('cascade');
            $table->integer('meses_pactados')->nullable(); // Para el cálculo automático
            $table->date('fecha_limite')->nullable();
            $table->string('estado_entrega')->default('Pendiente');
            $table->timestamps();
        }); */

        Schema::create('hitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->text('detalle');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('hitos');
        // Schema::dropIfExists('expediente_informes');
        Schema::dropIfExists('expediente_colaboradores');
        Schema::dropIfExists('expediente_proveedor');
        Schema::dropIfExists('expedientes');
    }
};
