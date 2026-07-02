<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Paso 1: Actualizar valores existentes para que coincidan con el ENUM actual
        // Si hay valores como 'failed' o 'pending_delivery', convertirlos a 'pending'
        DB::statement("UPDATE orders SET payment_status = 'pending' WHERE payment_status NOT IN ('pending', 'paid', 'pending_review')");

        // Paso 2: Modificar el ENUM agregando los nuevos valores
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'pending_review', 'pending_delivery') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revertir el ENUM al estado anterior
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'pending_review') DEFAULT 'pending'");
    }
};
