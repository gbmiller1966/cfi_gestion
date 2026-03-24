<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            // 2. Convertimos todos los campos de fecha a tipo DATE (sin hora)
            // Usamos ->change() para modificar las columnas existentes
            $table->date('f_ingreso_cfi')->nullable()->change();
            $table->date('f_ingreso_area')->nullable()->change();
            $table->date('f_derivacion_tecnico')->nullable()->change();
            $table->date('f_elevacion_tdr')->nullable()->change();
            $table->date('f_firma_jefe_tdr')->nullable()->change();
            $table->date('f_firma_director_tdr')->nullable()->change();
            $table->date('f_derivacion_compras')->nullable()->change();
            $table->date('f_inicio_contrato')->nullable()->change();
            $table->date('f_fin_contrato')->nullable()->change();
            $table->date('f_ingreso_informe_final')->nullable()->change();
            $table->date('f_aprobacion_contraparte')->nullable()->change();
            $table->date('f_aprobacion_jefe_tecnico')->nullable()->change();
            $table->date('f_aprobacion_director')->nullable()->change();
            $table->date('f_pase_gestion')->nullable()->change();
            $table->date('f_aprobacion_sec_gen')->nullable()->change();
            $table->date('f_envio_biblioteca')->nullable()->change();
            $table->date('f_envio_archivo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {

        });
    }
};




