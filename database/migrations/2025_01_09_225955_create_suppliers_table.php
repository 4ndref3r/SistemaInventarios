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
        Schema::create('suppliers', function (Blueprint $table) {
          $table->id();
          $table->string('ci_nit', 15)->nullable();
          $table->string('razonSocial', 50);
          $table->string('nombre', 50)->nullable();
          $table->string('celular', 20)->nullable();
          $table->string('email', 35)->nullable();
          $table->string('nro_cuenta', 50)->nullable();
          $table->string('direccion', 65)->nullable();
          $table->string('observacion', 75)->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
