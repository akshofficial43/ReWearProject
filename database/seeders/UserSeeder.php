<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cart;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users (role: admin or user)
        $users = [
            [
                'name' => 'John cina',
                'email' => 'john@rewear.com',
                'password' => Hash::make('password'),
                'address' => '456 Seller St, Seller City',
                'role' => 'user',
            ],
            
            [
                'name' => 'Alice walker',
                'email' => 'alice@rewear.com',
                'password' => Hash::make('password'),
                'address' => '321 Buyer St, Buyer City',
                'role' => 'user',
            ],
            
        ];

        foreach ($users as $userData) {
            if (!\App\Models\User::where('email', $userData['email'])->exists()) {
                $user = User::create($userData);
                Cart::create(['userId' => $user->userId]);
            }
        }
    }
}