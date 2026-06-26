<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Crear o actualizar el usuario repartidor
        $user = User::firstOrCreate(
            ['email' => 'repartidor@vizio.pe'],
            [
                'name' => 'Repartidor Vizio',
                'email' => 'repartidor@vizio.pe',
                'password' => Hash::make('repartidor123'),
                'is_delivery' => true,
                'is_admin' => false,
            ]
        );

        if (!$user->is_delivery) {
            $user->is_delivery = true;
            $user->save();
            $this->command->info('Usuario repartidor actualizado: ' . $user->email);
        } else {
            $this->command->info('Usuario repartidor ya existe: ' . $user->email);
        }
    }
}
