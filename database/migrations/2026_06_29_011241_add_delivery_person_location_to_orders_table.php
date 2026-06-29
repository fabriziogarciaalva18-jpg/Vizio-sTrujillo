<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Columna para el repartidor asignado (opcional, si no existe)
            if (!Schema::hasColumn('orders', 'delivery_person_id')) {
                $table->foreignId('delivery_person_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            // Última ubicación del repartidor
            if (!Schema::hasColumn('orders', 'delivery_person_lat')) {
                $table->decimal('delivery_person_lat', 10, 8)->nullable()->after('delivery_person_id');
            }
            if (!Schema::hasColumn('orders', 'delivery_person_lng')) {
                $table->decimal('delivery_person_lng', 11, 8)->nullable()->after('delivery_person_lat');
            }
            if (!Schema::hasColumn('orders', 'last_location_update')) {
                $table->timestamp('last_location_update')->nullable()->after('delivery_person_lng');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_person_id']);
            $table->dropColumn(['delivery_person_id', 'delivery_person_lat', 'delivery_person_lng', 'last_location_update']);
        });
    }
};
