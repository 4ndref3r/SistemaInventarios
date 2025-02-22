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
      Schema::create('manufacturing_materials', function (Blueprint $table) {
        $table->id();
        $table->foreignId('manufacturing_order_id');
        $table->foreignId('employee_id');
        $table->enum('tipo', ['ENTRADA','SALIDA','PERDIDA'])->default('SALIDA');
        $table->date('fecha');
        $table->foreignId('user_id');
        $table->string('observacion', 50)->nullable();
        $table->tinyInteger('estado')->default(1);
        $table->timestamps();
        $table->unsignedBigInteger('created_by')->nullable();
      });

      Schema::dropIfExists('manufacturing_material_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
