<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending','paid','failed','pending_review','pending_delivery','rejected') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending','paid','failed','pending_review','pending_delivery') DEFAULT 'pending'");
    }
};
