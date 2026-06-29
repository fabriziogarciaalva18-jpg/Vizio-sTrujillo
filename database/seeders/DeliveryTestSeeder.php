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
        $user = User::where('is_admin', false)->where('is_delivery', false)->first();
        if (!$user) {
            $user = User::first();
        }

        $product = Product::first();
        if (!$product) {
            $this->command->error('No hay productos. Crea uno primero.');
            return;
        }

        // 3 pedidos con fecha de hoy
        for ($i = 0; $i < 3; $i++) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . strtoupper(uniqid()),
                'order_type' => 'delivery',
                'delivery_type' => 'delivery',
                'status' => 'preparing',
                'delivery_date' => Carbon::today(),
                'delivery_address' => "Calle de prueba {$i+1} 123, Trujillo",
                'district' => 'Víctor Larco',
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

            $this->command->info("✅ Pedido #{$order->order_number} creado");
        }
    }
}
