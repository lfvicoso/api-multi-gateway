<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Gateway;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::create([
            'email' => 'admin@betalent.tech',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        // Create other role users for testing
        User::create([
            'email' => 'manager@betalent.tech',
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER,
        ]);

        User::create([
            'email' => 'finance@betalent.tech',
            'password' => Hash::make('password'),
            'role' => UserRole::FINANCE,
        ]);

        User::create([
            'email' => 'user@betalent.tech',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
        ]);

        // Create gateways
        Gateway::create([
            'name' => 'Gateway 1',
            'url' => env('GATEWAY_1_URL', 'http://gateway-mock:3001'),
            'is_active' => true,
            'priority' => 1,
            'type' => 'gateway1',
            'credentials' => [
                'email' => 'dev@betalent.tech',
                'token' => 'FEC9BB078BF338F464F96B48089EB498',
            ],
        ]);

        Gateway::create([
            'name' => 'Gateway 2',
            'url' => env('GATEWAY_2_URL', 'http://gateway-mock:3002'),
            'is_active' => true,
            'priority' => 2,
            'type' => 'gateway2',
            'credentials' => [
                'token' => 'tk_f2198cc671b5289fa856',
                'secret' => '3d15e8ed6131446ea7e3456728b1211f',
            ],
        ]);
    }
}

