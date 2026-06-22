<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductTestSeeder extends Seeder
{
    public function run()
    {
        // Crear categorías si no existen
        $categorias = [
            ['name' => 'Tortas', 'slug' => 'tortas', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Dulces', 'slug' => 'dulces', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Catering', 'slug' => 'catering', 'sort_order' => 3, 'is_active' => true],
        ];
        
        foreach ($categorias as $cat) {
            ProductCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
        
        // Obtener IDs de categorías
        $tortasId = ProductCategory::where('slug', 'tortas')->first()->id;
        $dulcesId = ProductCategory::where('slug', 'dulces')->first()->id;
        $cateringId = ProductCategory::where('slug', 'catering')->first()->id;
        
        // Productos de prueba
        $products = [
            [
                'category_id' => $tortasId,
                'name' => 'Torta Boombox',
                'slug' => 'torta-boombox',
                'description' => 'Torta rectangular con diseño retro de radiocasetera años 80',
                'product_type' => 'simple',
                'base_price' => 35000,
                'is_active' => true,
            ],
            [
                'category_id' => $tortasId,
                'name' => 'Torta Space Invaders',
                'slug' => 'torta-space-invaders',
                'description' => 'Torta pixel art de alienígenas clásicos',
                'product_type' => 'simple',
                'base_price' => 42000,
                'is_active' => true,
            ],
            [
                'category_id' => $dulcesId,
                'name' => 'Alfajores 80s',
                'slug' => 'alfajores-80s',
                'description' => 'Pack x6 alfajores con chispas de colores retro',
                'product_type' => 'simple',
                'base_price' => 6000,
                'is_active' => true,
            ],
            [
                'category_id' => $dulcesId,
                'name' => 'Cupcakes Arcade',
                'slug' => 'cupcakes-arcade',
                'description' => 'Cupcakes con diseño de joystick y flechas',
                'product_type' => 'simple',
                'base_price' => 4500,
                'is_active' => true,
            ],
            [
                'category_id' => $cateringId,
                'name' => 'Mesa Dulce 80s',
                'slug' => 'mesa-dulce-80s',
                'description' => 'Mesa temática para 15 personas con variedad de dulces',
                'product_type' => 'catering',
                'base_price' => 125000,
                'is_active' => true,
            ],
        ];
        
        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
        
        $this->command->info('Productos creados exitosamente');
    }
}