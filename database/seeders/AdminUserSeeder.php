<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@vizio.pe'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
        
        $this->command->info('Admin user created: admin@vizio.pe / admin123');
    }
}