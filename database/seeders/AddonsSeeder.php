<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonsSeeder extends Seeder
{
    public function run()
    {
        $addons = [
            // Decoraciones
            ['name' => 'Figura temática', 'description' => 'Figura comestible personalizada', 'price' => 1500, 'category' => 'decoration'],
            ['name' => 'Sprinkles de colores', 'description' => 'Decoración con grageas de colores', 'price' => 500, 'category' => 'decoration'],
            ['name' => 'Vela musical', 'description' => 'Vela que reproduce cumpleaños feliz', 'price' => 800, 'category' => 'decoration'],
            ['name' => 'Letrero personalizado', 'description' => 'Letrero con nombre o mensaje', 'price' => 1200, 'category' => 'decoration'],
            
            // Extras para catering
            ['name' => 'Carrito de palomitas', 'description' => 'Carrito vintage de popcorn', 'price' => 8000, 'category' => 'catering_extra'],
            ['name' => 'Máquina de algodón de azúcar', 'description' => 'Algodón de azúcar ilimitado', 'price' => 6000, 'category' => 'catering_extra'],
            ['name' => 'Foto call', 'description' => 'Set fotográfico temático', 'price' => 12000, 'category' => 'catering_extra'],
            ['name' => 'Mesa de cupcakes', 'description' => 'Display de cupcakes surtidos', 'price' => 15000, 'category' => 'catering_extra'],
            
            // Servicios adicionales
            ['name' => 'Mensaje especial grabado', 'description' => 'Placa comestible con mensaje', 'price' => 300, 'category' => 'service'],
            ['name' => 'Caja de regalo premium', 'description' => 'Caja especial para regalo', 'price' => 500, 'category' => 'service'],
            ['name' => 'Envío express', 'description' => 'Entrega en menos de 2 horas', 'price' => 1500, 'category' => 'service'],
        ];
        
        foreach ($addons as $addon) {
            Addon::create($addon);
        }
    }
}