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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 15)->default('C/F');
            $table->string('razonSocial', 50)->default('PROMAQ I+D');
            $table->string('gerente', 50)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 35)->nullable();
            $table->string('direccion', 65)->nullable();
            $table->string('cod_orden', 75)->nullable();
            $table->string('cod_empleado', 75)->nullable();
            $table->string('cod_factura', 75)->nullable();
            $table->string('cod_cotizacion', 75)->nullable();
            $table->integer('iva')->nullable();
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
        Schema::dropIfExists('settings');
    }
};
