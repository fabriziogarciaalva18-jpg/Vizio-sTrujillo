<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_type')) {
                $table->enum('delivery_type', ['pickup', 'delivery'])->default('delivery')->after('order_type');
            }
            if (!Schema::hasColumn('orders', 'address_lat')) {
                $table->decimal('address_lat', 10, 8)->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('orders', 'address_lng')) {
                $table->decimal('address_lng', 11, 8)->nullable()->after('address_lat');
            }
            if (!Schema::hasColumn('orders', 'delivery_distance')) {
                $table->decimal('delivery_distance', 10, 2)->nullable()->after('delivery_fee');
            }
            if (!Schema::hasColumn('orders', 'delivery_fee')) {
                // Cambiar a decimal para que sea dinámico
                $table->decimal('delivery_fee', 10, 2)->default(0)->change();
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'address_lat', 'address_lng', 'delivery_distance']);
        });
    }
};
