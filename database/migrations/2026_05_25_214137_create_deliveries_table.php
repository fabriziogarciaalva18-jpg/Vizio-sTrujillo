<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('delivery_person_id')->nullable()->constrained('users');
            $table->dateTime('scheduled_date');
            $table->time('scheduled_time_start');
            $table->time('scheduled_time_end');
            $table->enum('status', ['pending', 'assigned', 'in_route', 'delivered', 'failed'])->default('pending');
            $table->text('delivery_notes')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
};
