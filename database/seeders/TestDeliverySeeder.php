<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;

class TestDeliverySeeder extends Seeder
{
    public function run()
    {
        // Buscar un producto
        $product = Product::first();
        if (!$product) {
            $this->command->error('No hay productos en la base de datos. Primero crea un producto.');
            return;
        }

        // Buscar un usuario cliente
        $user = User::where('is_admin', false)->first();
        if (!$user) {
            $this->command->error('No hay usuarios clientes.');
            return;
        }

        // Crear 3 pedidos de prueba con diferentes estados
        $statuses = ['preparing', 'delivering', 'delivered'];

        foreach ($statuses as $i => $status) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TEST-' . strtoupper(uniqid()),
                'order_type' => 'delivery',
                'delivery_type' => 'delivery',
                'status' => $status,
                'delivery_date' => Carbon::today(),
                'delivery_address' => "Calle de prueba {$i} 123, Trujillo",
                'district' => 'Víctor Larco',
                'phone' => '987654321',
                'subtotal' => 100.00,
                'delivery_fee' => 8.00,
                'total' => 108.00,
                'payment_method' => 'yape',
                'payment_status' => 'pending',
                'delivered_at' => $status == 'delivered' ? now() : null,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'unit_price' => 100.00,
                'subtotal' => 100.00,
            ]);

            $this->command->info("✅ Pedido #{$order->order_number} creado con estado: {$status}");
        }
    }
}
