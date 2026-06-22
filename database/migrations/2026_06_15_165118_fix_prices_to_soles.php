<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    public function up()
    {
        $products = Product::all();
        foreach ($products as $product) {
            if ($product->base_price > 1000) {
                $product->base_price = $product->base_price / 100;
                $product->save();
            }
        }
    }

    public function down()
    {
        // No es necesario revertir
    }
};
