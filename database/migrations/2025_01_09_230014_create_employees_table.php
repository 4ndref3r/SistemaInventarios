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
        Schema::create('employees', function (Blueprint $table) {
          $table->id();
          $table->string('codigo', 50);
          $table->string('nombres', 50);
          $table->string('primer_apellido', 15);
          $table->string('segundo_apellido', 15)->nullable();
          $table->string('ci_nit', 25)->unique();
          $table->string('celular', 20)->nullable();
          $table->string('email', 35)->nullable();
          $table->string('direccion', 65)->nullable();
          $table->date('fecha_nacimiento');
          $table->date('fecha_ingreso');
          $table->date('fecha_cese')->nullable();
          $table->tinyInteger('estado')->default(1);
          $table->timestamps();
          $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
