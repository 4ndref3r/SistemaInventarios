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
        Schema::create('sales', function (Blueprint $table) {
          $table->id();
          $table->foreignId('customer_id');
          $table->string('nro_documento', 30);
          $table->string('nro_autorizacion', 35)->nullable();
          $table->date('fecha_venta');
          $table->enum('moneda', ['BOB', 'USD']);
          $table->decimal('tipo_cambio', 2, 2);
          $table->decimal('subtotal', 7, 2);
          $table->decimal('iva', 7, 2)->nullable();
          $table->decimal('descuento', 7, 2)->nullable();
          $table->decimal('total', 7, 2);
          $table->decimal('pagado', 7, 2);
          $table->decimal('saldo_pendiente', 7, 2);
          $table->enum('tipo_compra', ['CONTADO', 'CREDITO']);
          $table->enum('estado_pago', ['PENDIENTE', 'PARCIAL', 'PAGADO']);
          $table->enum('forma_pago', ['EFECTIVO', 'CHEQUE', 'QR', 'TRANSF. BANCARIA']);
          $table->foreignId('quotation_id');
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
        Schema::dropIfExists('sales');
    }
};
