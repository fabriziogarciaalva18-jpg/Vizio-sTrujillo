<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_type', ['pickup', 'delivery'])->default('delivery')->after('payment_method');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('delivery_type');
            $table->decimal('latitude', 10, 8)->nullable()->after('shipping_cost');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->boolean('address_validated')->default(false)->after('longitude');
            $table->string('district')->nullable()->change(); // Asegurar que sea nullable
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'shipping_cost', 'latitude', 'longitude', 'address_validated']);
        });
    }
};
