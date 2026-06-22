<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductConfiguration;
use Illuminate\Database\Seeder;

class ProductConfigurationsSeeder extends Seeder
{
    public function run()
    {
        $torta = Product::where('slug', 'torta-personalizada')->first();
        
        // Tamaños
        $sizes = [
            ['name' => 'Pequeño (15-20 porciones)', 'modifier' => 0],
            ['name' => 'Mediano (25-30 porciones)', 'modifier' => 3000],
            ['name' => 'Grande (35-40 porciones)', 'modifier' => 6000],
            ['name' => 'Extra Grande (50+ porciones)', 'modifier' => 10000],
        ];
        
        foreach ($sizes as $size) {
            ProductConfiguration::create([
                'product_id' => $torta->id,
                'config_type' => 'size',
                'name' => $size['name'],
                'price_modifier' => $size['modifier'],
                'sort_order' => 0,
            ]);
        }
        
        // Número de pisos
        $layers = [
            ['name' => '1 piso', 'modifier' => 0],
            ['name' => '2 pisos', 'modifier' => 8000],
            ['name' => '3 pisos', 'modifier' => 15000],
            ['name' => '4 pisos', 'modifier' => 25000],
        ];
        
        foreach ($layers as $layer) {
            ProductConfiguration::create([
                'product_id' => $torta->id,
                'config_type' => 'layers',
                'name' => $layer['name'],
                'price_modifier' => $layer['modifier'],
                'sort_order' => 0,
            ]);
        }
        
        // Sabores de bizcocho
        $flavors = [
            ['name' => 'Vainilla', 'modifier' => 0],
            ['name' => 'Chocolate', 'modifier' => 0],
            ['name' => 'Lúcuma', 'modifier' => 500],
            ['name' => 'Tres Leches', 'modifier' => 800],
            ['name' => 'Zanahoria', 'modifier' => 500],
            ['name' => 'Red Velvet', 'modifier' => 1000],
        ];
        
        foreach ($flavors as $flavor) {
            ProductConfiguration::create([
                'product_id' => $torta->id,
                'config_type' => 'flavor',
                'name' => $flavor['name'],
                'price_modifier' => $flavor['modifier'],
                'sort_order' => 0,
            ]);
        }
        
        // Rellenos
        $fillings = [
            ['name' => 'Sin relleno', 'modifier' => 0],
            ['name' => 'Dulce de Leche', 'modifier' => 1000],
            ['name' => 'Manjar Blanco', 'modifier' => 1000],
            ['name' => 'Crema de Chocolate', 'modifier' => 1500],
            ['name' => 'Frutas (surrtido)', 'modifier' => 2000],
        ];
        
        foreach ($fillings as $filling) {
            ProductConfiguration::create([
                'product_id' => $torta->id,
                'config_type' => 'filling',
                'name' => $filling['name'],
                'price_modifier' => $filling['modifier'],
                'sort_order' => 0,
            ]);
        }
        
        // Coberturas
        $coverings = [
            ['name' => 'Buttercream', 'modifier' => 0],
            ['name' => 'Fondant', 'modifier' => 3000],
            ['name' => 'Ganache de Chocolate', 'modifier' => 2500],
            ['name' => 'Merengue', 'modifier' => 2000],
        ];
        
        foreach ($coverings as $covering) {
            ProductConfiguration::create([
                'product_id' => $torta->id,
                'config_type' => 'covering',
                'name' => $covering['name'],
                'price_modifier' => $covering['modifier'],
                'sort_order' => 0,
            ]);
        }
    }
}