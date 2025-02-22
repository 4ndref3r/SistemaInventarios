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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 15)->unique();
            $table->string('nombre', 30);
            $table->string('descripcion', 80)->nullable();
            $table->enum('tipo_categoria', ['PRIMERO','SEGUNDO'])->default('PRIMERO');
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
        Schema::dropIfExists('categories');
    }
};
