<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Eliminar la columna product_id (si existe)
        if (Schema::hasColumn('product_configurations', 'product_id')) {
            Schema::table('product_configurations', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            });
        }

        // 2. Crear la tabla pivote
        if (!Schema::hasTable('configuration_product')) {
            Schema::create('configuration_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('configuration_id')->constrained('product_configurations')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['configuration_id', 'product_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('configuration_product');

        Schema::table('product_configurations', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        });
    }
};
