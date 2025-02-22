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
        Schema::create('table_sale_detail', function (Blueprint $table) {
          $table->id();
          $table->foreignId('sale_id');
          $table->foreignId('product_service_id');
          $table->decimal('cantidad',7,2);
          $table->decimal('precio_unitario',10,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_sale_detail');
    }
};
