<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class DeliveryTestSeeder extends Seeder
{
    public function run()
    {
        // Buscar un cliente
        $user = User::where('is_admin', false)->where('is_delivery', false)->first();
        if (!$user) {
            $user = User::first();
        }

        // Buscar un producto
        $product = Product::first();
        if (!$product) {
            $this->command->error('No hay productos. Crea uno primero.');
            return;
        }

        // Ubicaciones reales en Trujillo (La Libertad)
        $locations = [
            [
                'address' => 'Av. La Marina 123, Trujillo',
                'district' => 'Trujillo',
                'lat' => -8.1120,
                'lng' => -79.0288,
                'reference' => 'Cerca al parque',
            ],
            [
                'address' => 'Jr. Pizarro 456, Trujillo',
                'district' => 'Trujillo',
                'lat' => -8.1105,
                'lng' => -79.0260,
                'reference' => 'A espaldas de la catedral',
            ],
            [
                'address' => 'Calle Los Cedros 154, Víctor Larco Herrera',
                'district' => 'Víctor Larco',
                'lat' => -8.1191,
                'lng' => -79.0330,
                'reference' => 'Tienda Vizio\'s',
            ],
        ];

        // Crear 3 pedidos con diferentes ubicaciones
        foreach ($locations as $i => $loc) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . strtoupper(uniqid()),
                'order_type' => 'delivery',
                'delivery_type' => 'delivery',
                'status' => 'preparing',
                'delivery_date' => Carbon::today(),
                'delivery_address' => $loc['address'],
                'district' => $loc['district'],
                'address_lat' => $loc['lat'],
                'address_lng' => $loc['lng'],
                'delivery_reference' => $loc['reference'],
                'phone' => '987654321',
                'subtotal' => 100.00 + ($i * 20),
                'delivery_fee' => 8.00,
                'total' => 108.00 + ($i * 20),
                'payment_method' => 'yape',
                'payment_status' => 'pending',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'unit_price' => 100.00 + ($i * 20),
                'subtotal' => 100.00 + ($i * 20),
            ]);

            $this->command->info("✅ Pedido #{$order->order_number} creado en: {$loc['district']}");
        }
    }
}
