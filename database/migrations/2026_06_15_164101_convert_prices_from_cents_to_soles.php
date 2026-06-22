<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    public function up()
    {
        // Convertir los precios existentes: si está 35000, pasarlo a 350.00
        $products = Product::all();
        foreach ($products as $product) {
            // Si el precio es mayor a 1000, probablemente está en céntimos
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
