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
        Schema::table('table_material_employee', function (Blueprint $table) {
            $table->decimal('cantidad',7,2);
        });

        Schema::table('manufacturing_materials', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('table_material_employee', function (Blueprint $table) {
          $table->decimal('cantidad',7,2);
      });

      Schema::table('manufacturing_materials', function (Blueprint $table) {
          $table->dropColumn('cantidad');
      });
    }
};
