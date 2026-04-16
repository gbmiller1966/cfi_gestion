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
            $table->decimal('monto_solicitud_provincial', 15, 2)->nullable()->change();
            $table->decimal('monto_cfi', 15, 2)->nullable()->change();
            $table->decimal('monto_convenido', 15, 2)->nullable()->change();
            $table->decimal('monto_imputado', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            //
        });
    }
};
