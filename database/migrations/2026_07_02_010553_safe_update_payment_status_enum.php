<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Añadir una columna temporal con el nuevo ENUM
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status_new', ['pending', 'paid', 'failed', 'pending_review', 'pending_delivery'])
                  ->default('pending')
                  ->after('payment_status');
        });

        // 2. Copiar los datos, mapeando valores no válidos
        DB::statement("
            UPDATE orders
            SET payment_status_new = CASE
                WHEN payment_status IN ('pending', 'paid', 'pending_review', 'pending_delivery')
                    THEN payment_status
                WHEN payment_status = 'rejected' THEN 'failed'
                ELSE 'pending'
            END
        ");

        // 3. Eliminar la columna antigua
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });

        // 4. Renombrar la nueva columna
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('payment_status_new', 'payment_status');
        });
    }

    public function down()
    {
        // Revertir (opcional)
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'pending_review', 'pending_delivery'])->default('pending');
        });
    }
};
