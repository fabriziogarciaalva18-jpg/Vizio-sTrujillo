<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->enum('order_type', ['delivery', 'pickup', 'catering'])->default('delivery');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'delivered', 'cancelled'])->default('pending');
            $table->date('delivery_date');
            $table->time('delivery_time_start')->nullable();
            $table->time('delivery_time_end')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('district')->nullable();
            $table->string('reference_point')->nullable();
            $table->string('phone');
            $table->string('alternative_phone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['yape', 'plin', 'transferencia', 'contraentrega', 'tarjeta']);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('special_instructions')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
