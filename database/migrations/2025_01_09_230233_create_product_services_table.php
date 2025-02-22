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
        Schema::create('product_services', function (Blueprint $table) {
          $table->id();
          $table->string('codigo', 15)->unique();
          $table->string('nombre', 40);
          $table->string('descripcion', 50)->nullable();
          $table->foreignId('category_id');
          $table->decimal('costo_neto', 7, 2);
          $table->decimal('mano_obra', 7, 2);
          $table->decimal('margen_ganancia', 7, 2);
          $table->decimal('precio_venta', 7, 2);
          $table->boolean('tiene_iva')->default(false);
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
        Schema::dropIfExists('product_services');
    }
};
