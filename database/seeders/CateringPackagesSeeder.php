<?php

namespace Database\Seeders;

use App\Models\CateringPackage;
use Illuminate\Database\Seeder;

class CateringPackagesSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name' => 'Pack Básico - Coffee Break',
                'description' => 'Ideal para reuniones empresariales cortas',
                'min_guests' => 10,
                'max_guests' => 30,
                'price_per_person' => 2500,
                'included_services' => json_encode([
                    'café y té ilimitados',
                    'variedad de galletas',
                    'cupcakes surtidos',
                    'agua con gas',
                    'servicio de mesa',
                ]),
            ],
            [
                'name' => 'Pack Premium - Lunch Ejecutivo',
                'description' => 'Completo para almuerzos empresariales',
                'min_guests' => 15,
                'max_guests' => 60,
                'price_per_person' => 4500,
                'included_services' => json_encode([
                    'buffet de entrada',
                    'plato principal (2 opciones)',
                    'postre especial',
                    'bebidas (gaseosas/jugos)',
                    'mesa de dulces básica',
                    'personal de atención',
                    'vajilla incluida',
                ]),
            ],
            [
                'name' => 'Pack Deluxe - Evento Social',
                'description' => 'Todo lo necesario para fiestas y celebraciones',
                'min_guests' => 20,
                'max_guests' => 100,
                'price_per_person' => 7500,
                'included_services' => json_encode([
                    'cocktail de bienvenida',
                    'estación de sushi',
                    'carrito de popcorn',
                    'mesa de quesos y frutas',
                    'barra de postres',
                    'barra libre (pisco, vino, cerveza)',
                    'decoración temática',
                    'música ambiental',
                    'staff completo',
                ]),
            ],
        ];
        
        foreach ($packages as $package) {
            CateringPackage::create($package);
        }
    }
}