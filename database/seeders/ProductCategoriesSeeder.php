<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Tortas Personalizadas',
                'slug' => 'tortas',
                'description' => 'Tortas diseñadas especialmente para ti, con tamaños y sabores a elegir',
                'icon' => 'cake',
                'sort_order' => 1,
            ],
            [
                'name' => 'Pasteles y Postres',
                'slug' => 'pasteles',
                'description' => 'Deliciosos pasteles, cheesecakes y postres individuales',
                'icon' => 'cupcake',
                'sort_order' => 2,
            ],
            [
                'name' => 'Galería Dulce',
                'slug' => 'galletas',
                'description' => 'Galletas artesanales, alfajores y bocaditos',
                'icon' => 'cookie',
                'sort_order' => 3,
            ],
            [
                'name' => 'Catering y Eventos',
                'slug' => 'catering',
                'description' => 'Servicio completo para eventos, reuniones y celebraciones',
                'icon' => 'party',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}