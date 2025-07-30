<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users with different phone formats
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'phone' => '628123456789',
                'nickname' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'username' => 'seller1',
                'email' => 'seller1@example.com',
                'phone' => '081234567890',
                'nickname' => 'Seller 1',
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'username' => 'customer1',
                'email' => 'customer1@example.com',
                'phone' => '81234567891',
                'nickname' => 'Customer 1',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'username' => 'testuser',
                'email' => 'test@example.com',
                'phone' => '628765432109',
                'nickname' => 'Test User',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Test users created successfully!');
        $this->command->info('Admin: admin@example.com / admin123');
        $this->command->info('Seller: seller1@example.com / seller123');
        $this->command->info('Customer: customer1@example.com / customer123');
        $this->command->info('Test: test@example.com / password123');
    }
}