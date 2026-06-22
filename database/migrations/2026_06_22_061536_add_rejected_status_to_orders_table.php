<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Modificar el enum para incluir 'rejected'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','delivering','delivered','cancelled','rejected') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revertir al enum original
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','preparing','delivering','delivered','cancelled') DEFAULT 'pending'");
    }
};
