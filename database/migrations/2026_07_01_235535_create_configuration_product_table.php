<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuration_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_configuration_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicados
            $table->unique(['product_configuration_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuration_product');
    }
};
