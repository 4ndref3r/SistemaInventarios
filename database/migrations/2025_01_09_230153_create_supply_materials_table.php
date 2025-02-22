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
        Schema::create('supply_materials', function (Blueprint $table) {
          $table->id();
          $table->string('codigo', 15)->unique();
          $table->string('nombre', 40);
          $table->string('descripcion', 50)->nullable();
          $table->foreignId('category_id');
          $table->foreignId('unit_measure_id');
          $table->decimal('stock_actual', 7, 2);
          $table->decimal('stock_minimo', 7, 2)->nullable();
          $table->decimal('stock_comprometido', 7, 2)->nullable();
          $table->decimal('precio_compra', 7, 2);
          $table->decimal('precio_venta', 7, 2)->nullable();
          $table->string('ubicacion', 100)->nullable();
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
        Schema::dropIfExists('supply_materials');
    }
};
