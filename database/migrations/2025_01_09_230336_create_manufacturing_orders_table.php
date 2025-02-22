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
        Schema::create('manufacturing_orders', function (Blueprint $table) {
          $table->id();
          $table->string('codigo', 15);
          $table->date('fecha_inicio');
          $table->date('fecha_fin')->nullable();
          $table->enum('estado_proceso', ['EN PROCESO','TERMINADO','EN DEMORA'])->default('EN PROCESO');
          $table->foreignId('product_service_id');
          $table->decimal('cantidad', 3, 2);
          $table->string('observacion', 50)->nullable();
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
        Schema::dropIfExists('manufacturing_orders');
    }
};
