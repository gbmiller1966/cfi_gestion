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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('cuit')->unique();
            $table->string('numero_proveedor')->nullable();
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('provincia_id')->constrained('provincias');
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_email')->nullable();
            $table->string('contacto_celular')->nullable();
            $table->string('doc_proveedor')->nullable(); // Ruta o referencia a la documentación del proveedor
            $table->string('doc_dotacion')->nullable(); // Ruta o referencia a la documentación del proveedor
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
