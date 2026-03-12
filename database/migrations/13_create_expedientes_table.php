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
$table->string('gde_numero')->nullable(); // Número de expediente GDE
$table->text('objeto');

// --- Montos ---
$table->decimal('monto_solicitud_provincial', 15, 2)->default(0);
$table->decimal('monto_convenido', 15, 2)->default(0);
$table->decimal('monto_cfi', 15, 2)->default(0);

// --- Relaciones (Tablas Maestras) ---
$table->foreignId('user_id')->constrained('users'); // Técnico responsable (Dueño)
$table->foreignId('region_id')->constrained('regiones');
$table->foreignId('provincia_id')->constrained('provincias');
$table->foreignId('localidad_id')->constrained('localidades');
$table->foreignId('contraparte_id')->constrained('contrapartes_provinciales');
$table->foreignId('asignacion_id')->constrained('asignaciones_presupuestarias');
$table->foreignId('tema_id')->constrained('temas_estrategicos');
$table->foreignId('tipo_contrato_id')->constrained('tipos_contrato');
$table->foreignId('estado_contrato_id')->constrained('estados_contrato');

// --- Circuito de Tiempos (Fechas de cada etapa) ---
// Se registran para calcular los días transcurridos
$table->timestamp('f_ingreso_cfi')->nullable();
$table->timestamp('f_ingreso_area')->nullable();
$table->timestamp('f_derivacion_tecnico')->nullable();
$table->timestamp('f_elevacion_tdr')->nullable();
$table->timestamp('f_firma_jefe_tdr')->nullable();
$table->timestamp('f_firma_director_tdr')->nullable();
$table->string('gde_tdr')->nullable(); // Registro GDE de la etapa vi

$table->timestamp('f_derivacion_compras')->nullable();
$table->date('f_inicio_contrato')->nullable(); // Fecha de inicio real
$table->date('f_fin_contrato')->nullable();    // Fecha de fin real

$table->timestamp('f_ingreso_informe_final')->nullable();
$table->timestamp('f_aprobacion_contraparte')->nullable();
$table->timestamp('f_aprobacion_jefe_tecnico')->nullable();
$table->timestamp('f_aprobacion_director')->nullable();
$table->string('gde_aprobacion_dir')->nullable(); // Registro GDE de la etapa xii

$table->timestamp('f_pase_gestion')->nullable();
$table->timestamp('f_aprobacion_sec_gen')->nullable();
$table->string('gde_sec_gen')->nullable(); // Registro GDE de la etapa xiv

$table->timestamp('f_envio_biblioteca')->nullable();
$table->string('gde_biblioteca')->nullable(); // Registro GDE de la etapa xv

$table->timestamp('f_envio_archivo')->nullable();
$table->string('gde_archivo')->nullable(); // Registro GDE de la etapa xvi

// --- Estado de Control ---
$table->integer('etapa_actual')->default(1); // Para saber en qué punto del 1 al 16 está

$table->timestamps();
$table->softDeletes(); // Recomendado para expedientes públicos
});

// --- Tablas Pivot ---

// Proveedores (Muchos a Muchos: un expediente puede tener varios proveedores)
Schema::create('expediente_proveedor', function (Blueprint $table) {
$table->id();
$table->foreignId('expediente_id')->constrained()->onDelete('cascade');
$table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
});

// Colaboradores (Técnicos que intervienen además del dueño)
Schema::create('expediente_colaboradores', function (Blueprint $table) {
$table->id();
$table->foreignId('expediente_id')->constrained()->onDelete('cascade');
$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
});

// Informes pactados (Tabla pivot con los informes del contrato)
Schema::create('expediente_informes', function (Blueprint $table) {
$table->id();
$table->foreignId('expediente_id')->constrained()->onDelete('cascade');
$table->foreignId('informe_id')->constrained('informes_maestros')->onDelete('cascade');
$table->string('estado_entrega')->default('Pendiente');
$table->date('fecha_limite')->nullable();
$table->timestamps();
});

// Hitos (Novedades diarias)
Schema::create('hitos', function (Blueprint $table) {
$table->id();
$table->foreignId('expediente_id')->constrained()->onDelete('cascade');
$table->text('detalle');
$table->timestamps();
});
}

public function down(): void
{
Schema::dropIfExists('hitos');
Schema::dropIfExists('expediente_informes');
Schema::dropIfExists('expediente_colaboradores');
Schema::dropIfExists('expediente_proveedor');
Schema::dropIfExists('expedientes');
}
};
