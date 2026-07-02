<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Eliminar la tabla si existe (para evitar conflictos)
        if (Schema::hasTable('configuration_product')) {
            Schema::dropIfExists('configuration_product');
        }

        // Crear la tabla con la estructura correcta
        Schema::create('configuration_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuration_id')
                  ->constrained('product_configurations')
                  ->onDelete('cascade');
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->timestamps();
            // Evitar duplicados
            $table->unique(['configuration_id', 'product_id'], 'config_product_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuration_product');
    }
};
