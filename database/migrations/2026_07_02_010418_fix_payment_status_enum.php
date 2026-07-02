<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Convertir cualquier 'rejected' a 'failed' (o a 'pending' si prefieres)
        DB::table('orders')
            ->where('payment_status', 'rejected')
            ->update(['payment_status' => 'failed']);

        // 2. También convertir otros valores no permitidos si existieran
        DB::table('orders')
            ->whereNotIn('payment_status', ['pending', 'paid', 'pending_review', 'pending_delivery', 'failed'])
            ->update(['payment_status' => 'pending']);

        // 3. Ahora modificar el ENUM
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'pending_review', 'pending_delivery') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revertir al ENUM anterior
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'pending_review', 'pending_delivery') DEFAULT 'pending'");
    }
};
