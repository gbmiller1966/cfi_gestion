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
        Schema::table('users', function (Blueprint $table) {
        $table->string('apellido')->after('nombre')->nullable();
        $table->string('celular')->nullable();
        $table->foreignId('direccion_id')->nullable()->constrained('direcciones');
        $table->foreignId('area_id')->nullable()->constrained('areas');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
