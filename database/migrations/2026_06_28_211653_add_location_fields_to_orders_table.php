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
        Schema::table('orders', function (Blueprint $table) {
            // Tipo de entrega (recojo en tienda o delivery)
            if (!Schema::hasColumn('orders', 'delivery_type')) {
                $table->enum('delivery_type', ['pickup', 'delivery'])->default('delivery')->after('order_type');
            }

            // Coordenadas de la dirección de entrega
            if (!Schema::hasColumn('orders', 'address_lat')) {
                $table->decimal('address_lat', 10, 8)->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('orders', 'address_lng')) {
                $table->decimal('address_lng', 11, 8)->nullable()->after('address_lat');
            }

            // Distancia calculada (km)
            if (!Schema::hasColumn('orders', 'delivery_distance')) {
                $table->decimal('delivery_distance', 10, 2)->nullable()->after('delivery_fee');
            }

            // Referencia de entrega (indicaciones al repartidor)
            if (!Schema::hasColumn('orders', 'delivery_reference')) {
                $table->text('delivery_reference')->nullable()->after('special_instructions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type',
                'address_lat',
                'address_lng',
                'delivery_distance',
                'delivery_reference'
            ]);
        });
    }
};
