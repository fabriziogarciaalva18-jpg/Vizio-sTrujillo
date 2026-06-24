<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE product_configurations MODIFY COLUMN config_type ENUM('size','layers','flavor','filling','covering','shape','color','toppings','message','decoration') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE product_configurations MODIFY COLUMN config_type ENUM('size','layers','flavor','filling','covering') NOT NULL");
    }
};
