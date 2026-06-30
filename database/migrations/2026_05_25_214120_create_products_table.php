<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->enum('product_type', ['configurable', 'simple', 'catering'])->default('simple');
            $table->boolean('has_sizes')->default(false);
            $table->boolean('has_layers')->default(false);
            $table->boolean('has_flavors')->default(false);
            $table->boolean('has_fillings')->default(false);
            $table->boolean('has_coverings')->default(false);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
