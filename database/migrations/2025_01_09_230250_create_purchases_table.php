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
        Schema::create('purchases', function (Blueprint $table) {
          $table->id();
          $table->string('nro_documento', 30);
          $table->string('nro_autorizacion', 35)->nullable();
          $table->foreignId('supplier_id');
          $table->date('fecha_compra');
          $table->date('fecha_vencimiento')->nullable();
          $table->enum('moneda', ['BOB', 'USD']);
          $table->decimal('tipo_cambio', 2, 2);
          $table->decimal('subtotal', 7, 2);
          $table->decimal('iva', 7, 2)->nullable();
          $table->decimal('total', 7, 2);
          $table->decimal('pagado', 7, 2);
          $table->decimal('pago_pendiente', 7, 2);
          $table->enum('tipo_compra', ['CONTADO', 'CREDITO']);
          $table->enum('estado_pago', ['PENDIENTE', 'PARCIAL', 'PAGADO']);
          $table->date('fecha_plazo')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
