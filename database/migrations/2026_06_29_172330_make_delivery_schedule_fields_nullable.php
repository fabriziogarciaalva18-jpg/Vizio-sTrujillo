<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Hacer nullable las columnas de horario
            $table->dateTime('scheduled_date')->nullable()->change();
            $table->time('scheduled_time_start')->nullable()->change();
            $table->time('scheduled_time_end')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dateTime('scheduled_date')->nullable(false)->change();
            $table->time('scheduled_time_start')->nullable(false)->change();
            $table->time('scheduled_time_end')->nullable(false)->change();
        });
    }
};
