<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending', 'pending_review', 'paid', 'failed', 'pending_delivery') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('pending', 'pending_review', 'paid', 'failed') DEFAULT 'pending'");
    }
};
