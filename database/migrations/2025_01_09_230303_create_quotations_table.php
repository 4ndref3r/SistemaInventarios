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
        Schema::create('quotations', function (Blueprint $table) {
          $table->id();
          $table->foreignId('customer_id');
          $table->date('fecha_emision');
          $table->date('fecha_validez')->nullable();
          $table->enum('moneda', ['BOB', 'USD']);
          $table->decimal('tipo_cambio', 2, 2);
          $table->decimal('subtotal', 7, 2);
          $table->decimal('iva', 7, 2)->nullable();
          $table->decimal('descuento', 7, 2)->nullable();
          $table->decimal('total', 7, 2);
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
        Schema::dropIfExists('quotations');
    }
};
