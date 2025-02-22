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
        Schema::create('table_material_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_material_id');
            $table->foreignId('supply_material_id');
            $table->decimal('cantidad',7,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_material_employee');
    }
};
