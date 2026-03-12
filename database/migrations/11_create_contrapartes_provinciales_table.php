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
    Schema::create('contrapartes_provinciales', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('apellido');
    $table->string('email');
    $table->string('email_particular')->nullable();
    $table->string('celular')->nullable();
    $table->string('dependencia')->nullable();
    $table->string('cargo')->nullable();
    $table->foreignId('provincia_id')->constrained('provincias');
    $table->text('observaciones')->nullable();
    $table->timestamps();
});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
    Schema::dropIfExists('contrapartes_provinciales');
}
};
